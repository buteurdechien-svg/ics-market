<?php
/** ICS Market — configuration centrale (laboratoire). */
declare(strict_types=1);

return [
    'app_name' => getenv('APP_NAME') ?: 'ICS Market',
    'app_env'  => getenv('APP_ENV') ?: 'lab',
    'currency' => 'FCFA',

    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'ics_market',
        'user'    => getenv('DB_USER') ?: 'ics_app',
        'pass'    => getenv('DB_PASSWORD') ?: 'ics_app_lab',
        'charset' => 'utf8mb4',
    ],

    'mail' => [
        'host' => getenv('MAIL_HOST') ?: '127.0.0.1',
        'port' => (int)(getenv('MAIL_PORT') ?: 1025),
        'from' => getenv('MAIL_FROM') ?: 'no-reply@ics-market.local',
    ],

    'storage_path' => dirname(__DIR__) . '/storage',
    'uploads_url'  => '/uploads',
];
