<?php
header('Content-Type: text/plain; charset=utf-8');
$p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($p === '/' || $p === '/secret' || $p === '/internal') {
    echo "=== ICS Market internal metadata service ===\n";
    echo "service: ics-market-internal\n";
    echo "internal_api_key: ICS-MKT-INTERNAL-7a1c9b4e\n";
    echo "db_replica: 10.10.0.5:3306\n";
    echo "note: internal use only - do not expose to clients.\n";
} else { http_response_code(404); echo "not found"; }
