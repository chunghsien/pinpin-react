<?php

use Laminas\Db\Adapter\Adapter;

return [
    'db' => [
        'adapters' => [
            Adapter::class => [
                'driver' => 'pdo_mysql',
                'database' => 'laminas',
                'username' => 'homestead',
                'hostname' => '192.168.10.10',
                'password' => 'secret',
                'options' => [
                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                ],
                'charset' => 'utf8mb4',
                'prefix' => '',
                'profiling' => preg_match('/\.debug$/i', APP_ENV),
            ],
        ],
    ],
];
