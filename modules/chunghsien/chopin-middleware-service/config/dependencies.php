<?php

use Chopin\Middleware\MiddlewareAbstractServiceFactory;
use Chopin\MiddlewareService\MiddlewareServiceAbstractServiceFactory;

return [
    'dependencies' => [
        'abstract_factories' => [
            MiddlewareAbstractServiceFactory::class,
            MiddlewareServiceAbstractServiceFactory::class,
        ],
    ],
];
