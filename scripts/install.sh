#!/usr/bin/env bash
#
# ICS Market — installation sur Ubuntu Server 22.04 LTS.
# Installe Apache 2, PHP 8.3, MySQL 8 et Mailpit, puis déploie le laboratoire.
#
# Usage :  sudo ./scripts/install.sh
#
# ATTENTION : application VOLONTAIREMENT VULNÉRABLE. À n'installer que dans
# une VM isolée (réseau NAT ou Host-only), jamais exposée sur Internet.

set -euo pipefail
if [[ $EUID -ne 0 ]]; then echo "Lancez avec sudo." >&2; exit 1; fi

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"
DB_NAME="ics_market"; DB_USER="ics_app"; DB_PASS="ics_app_lab"

echo "==> ICS Market — installation dans : $PROJECT_DIR"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y software-properties-common curl unzip apache2 mysql-server

echo "==> PHP 8.3..."
if ! php -v 2>/dev/null | grep -q "PHP 8.3"; then
  add-apt-repository -y ppa:ondrej/php; apt-get update -y
fi
apt-get install -y php8.3 libapache2-mod-php8.3 php8.3-mysql php8.3-mbstring php8.3-curl php8.3-gd

echo "==> Mailpit..."
command -v mailpit >/dev/null 2>&1 || curl -fsSL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh | bash

echo "==> Fichier .env..."
if [[ ! -f .env ]]; then
  [[ -f .env.example ]] && cp .env.example .env || cat > .env <<ENV
APP_NAME="ICS Market"
APP_ENV=lab
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASSWORD=${DB_PASS}
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM=no-reply@ics-market.local
ENV
fi

echo "==> MySQL..."
systemctl enable --now mysql
mysql <<SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
mysql "${DB_NAME}" < database/schema.sql
DB_HOST=127.0.0.1 DB_USER="${DB_USER}" DB_PASSWORD="${DB_PASS}" DB_NAME="${DB_NAME}" php database/seed.php

echo "==> Dossiers de stockage et uploads (accessibles en écriture au serveur)..."
mkdir -p storage/logs storage/invoices public/uploads
chown -R www-data:www-data storage public/uploads
chmod -R 0775 storage public/uploads

# Rendre le chemin du projet traversable par Apache (home en 0750 -> 403).
d="$PROJECT_DIR"
while [[ "$d" != "/" ]]; do chmod o+x "$d" 2>/dev/null || true; d="$(dirname "$d")"; done

echo "==> Apache..."
cat > /etc/apache2/sites-available/ics-market.conf <<CONF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot ${PROJECT_DIR}/public
    SetEnv DB_HOST 127.0.0.1
    SetEnv DB_USER ${DB_USER}
    SetEnv DB_PASSWORD ${DB_PASS}
    SetEnv DB_NAME ${DB_NAME}
    SetEnv MAIL_HOST 127.0.0.1
    SetEnv MAIL_PORT 1025
    <Directory ${PROJECT_DIR}/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/ics-market-error.log
    CustomLog \${APACHE_LOG_DIR}/ics-market-access.log combined
</VirtualHost>
CONF
a2enmod rewrite headers php8.3 >/dev/null 2>&1 || true
a2dissite 000-default >/dev/null 2>&1 || true
a2ensite ics-market >/dev/null 2>&1
systemctl restart apache2

echo "==> Service interne (cible SSRF de démonstration)..."
bash scripts/ssrf-service.sh install || true
echo "==> Mailpit..."
bash scripts/mailpit-service.sh install || true

IP=$(hostname -I | awk '{print $1}')
cat <<DONE

============================================================
  ICS Market est installé.
  Site          : http://localhost/        (dans la VM)
  Mailpit (OTP) : http://localhost:8025/
  API           : http://localhost/api/v1/
  Depuis Kali (réseau host-only) : remplacez localhost par ${IP}
  Réinitialiser : sudo ./scripts/reset-lab.sh
============================================================
DONE
