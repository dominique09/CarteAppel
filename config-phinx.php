<?php
require "App/Config.php";

use APP\Config;

return [
    'paths' => [
        'migrations' => 'Migrations',
        'seeds' => 'Seeds'
    ],
    'migration_base_class' => '\Core\Migration',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'dev' => [
            'adapter' => 'mysql',
            'host' => Config::DB_HOST,
            'name' => Config::DB_NAME,
            'user' => Config::DB_MIGRATION_USER,
            'pass' => Config::DB_MIGRATION_PASS,
            'port' => Config::DB_PORT
        ]
    ]
];