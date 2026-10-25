#!/usr/bin/env bash
# ICS Market — remise à zéro du laboratoire.
set -euo pipefail
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"
DB_NAME="${DB_NAME:-ics_market}"; DB_USER="${DB_USER:-ics_app}"; DB_PASS="${DB_PASSWORD:-ics_app_lab}"
echo "==> Rechargement du schéma..."
mysql "${DB_NAME}" < database/schema.sql
echo "==> Données de démonstration..."
DB_HOST=127.0.0.1 DB_USER="${DB_USER}" DB_PASSWORD="${DB_PASS}" DB_NAME="${DB_NAME}" php database/seed.php
echo "==> Nettoyage uploads / factures..."
find public/uploads -type f ! -name '.gitkeep' ! -name '.htaccess' -delete 2>/dev/null || true
rm -f storage/invoices/* storage/logs/* 2>/dev/null || true
echo "==> Laboratoire réinitialisé."
