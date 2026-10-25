#!/usr/bin/env bash
set -euo pipefail
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
case "${1:-install}" in
  install)
    cat > /etc/systemd/system/ics-mkt-ssrf.service <<SERVICE
[Unit]
Description=ICS Market - service interne SSRF (demo)
After=network.target
[Service]
ExecStart=/usr/bin/php -S 127.0.0.1:9000 -t ${PROJECT_DIR}/scripts/ssrf-internal
Restart=always
User=www-data
[Install]
WantedBy=multi-user.target
SERVICE
    systemctl daemon-reload; systemctl enable --now ics-mkt-ssrf
    echo "Service SSRF interne sur 127.0.0.1:9000" ;;
  stop) systemctl stop ics-mkt-ssrf || true ;;
esac
