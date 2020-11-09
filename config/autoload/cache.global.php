<?php

use Chopin\LaminasDb\RowGateway\RowGateway;
use Laminas\Cache\Storage\Plugin\Serializer;
use Laminas\Cache\Storage\StorageInterface;

return [
    'caches' => [
        StorageInterface::class => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'dir_level' => 1,
                    'cache_dir' => 'storage/cache/app',
                    'ttl' => 31536000, //one years
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                        'serializer_options' => [
                            'unserialize_class_whitelist' => [
                                RowGateway::class,
                            ],
                        ],

                    ],
                ],
            ],
        ],
    ],
    'env_cache' => [
        'vars' => preg_match('/^production/i', APP_ENV),
        'db' => preg_match('/^production/i', APP_ENV),
    ],
    
];
