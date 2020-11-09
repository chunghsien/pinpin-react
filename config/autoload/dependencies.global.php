<?php

use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Chopin\LaminasDb\Tablegateway\TablegatewayAbstractServiceFactory;
use Chopin\LaminasDb\Services\DbServiceAbstractServiceFactory;
use Chopin\Middleware\MiddlewareAbstractServiceFactory;

return [
    'dependencies' => [
        'delegators' => [],
        'invokables' => [],
        'abstract_factories' => [
            AdapterAbstractServiceFactory::class,
            TablegatewayAbstractServiceFactory::class,
            DbServiceAbstractServiceFactory::class,
            MiddlewareAbstractServiceFactory::class,
        ],
        'alias' => [],
        'factories' => [],
    ],
];
