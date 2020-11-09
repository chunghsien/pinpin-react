<?php

use Twig\NodeVisitor\OptimizerNodeVisitor;

return [
    'templates' => [
        'paths' => [
            'app' => ['resources/templates/app'],
            'error' => ['resources/templates/error'],
            //'layout' => ['resources/templates/layout'],
        ],
    ],
    'twig' => [
        'autoescape' => 'html',
        'cache_dir' => APP_ENV === 'production' ? 'storage/cache/twig' : false,
        'debug' => APP_ENV != 'production',
        'timezone' => "Asia/Taipei",
        'auto_reload' => APP_ENV !== 'production',
        'optimizations' => APP_ENV == 'production' ? OptimizerNodeVisitor::OPTIMIZE_ALL : OptimizerNodeVisitor::OPTIMIZE_NONE,
    ],
];
