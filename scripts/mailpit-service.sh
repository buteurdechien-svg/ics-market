#!/usr/bin/env bash
set -euo pipefail
case "${1:-install}" in
  install)
    BIN="$(command -v mailpit || echo /usr/local/bin/mailpit)"
    cat > /etc/systemd/system/mailpit.service <<SERVICE
[Unit]
Description=Mailpit (capture e-mails de test ICS Market)
After=network.target
[Service]
ExecStart=${BIN} --smtp 127.0.0.1:1025 --listen 0.0.0.0:8025
Restart=always
User=www-data
[Install]
WantedBy=multi-user.target
SERVICE
    systemctl daemon-reload; systemctl enable --now mailpit
    echo "Mailpit (SMTP 127.0.0.1:1025, web 0.0.0.0:8025)" ;;
  stop) systemctl stop mailpit || true ;;
esac
