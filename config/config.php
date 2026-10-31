<?php
return [
    'app_name' => getenv('APP_NAME') ?: 'MediCitas',
    'base_path' => rtrim(getenv('APP_BASE_PATH') ?: '', '/'),
    'db' => [
        'driver' => getenv('DB_CONNECTION') ?: 'mysql',
        'sqlite' => getenv('DB_DATABASE') ?: dirname(__DIR__) . '/storage/medicitas.sqlite',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'medicitas',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
];
