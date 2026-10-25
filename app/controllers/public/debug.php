<?php
/** Page de diagnostic interne. */
header('Content-Type: text/plain; charset=utf-8');
$cfg = config();
echo "ICS Market — diagnostic interne\n" . str_repeat('=', 40) . "\n";
echo 'PHP          : ' . PHP_VERSION . "\n";
echo 'Serveur      : ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'n/a') . "\n";
echo 'OS           : ' . php_uname() . "\n";
echo 'App env      : ' . $cfg['app_env'] . "\n";
echo 'DB DSN       : mysql://' . $cfg['db']['user'] . '@' . $cfg['db']['host'] . ':' . $cfg['db']['port'] . '/' . $cfg['db']['name'] . "\n";
echo 'Mail host    : ' . $cfg['mail']['host'] . ':' . $cfg['mail']['port'] . "\n";
echo 'Storage path : ' . $cfg['storage_path'] . "\n";
echo 'Doc root     : ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'n/a') . "\n";
echo 'Uploads      : ' . $cfg['uploads_url'] . "\n";
echo "\nRoutes API : /api/v1/login, /api/v1/products, /api/v1/orders/{id}, /api/v1/me, /api/v1/profile\n";
