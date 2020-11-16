<?php

use Chopin\Users;

return
[
    'dependencies' => [
        'invokables' => [],
        'factories' => [
            Users\JwtAuthenticationMiddleware::class => Users\JwtAuthenticationMiddlewareFactory::class,
        ],
    ],
];
