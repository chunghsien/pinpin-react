<?php

use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Chopin\LaminasDb\TableGateway\TableGatewayAbstractServiceFactory;
use Chopin\LaminasDb\Services\DbServiceAbstractServiceFactory;
//use Chopin\Middleware\MiddlewareAbstractServiceFactory;

return [
    'dependencies' => [
        'delegators' => [],
        'invokables' => [],
        'abstract_factories' => [
            AdapterAbstractServiceFactory::class,
            TableGatewayAbstractServiceFactory::class,
            DbServiceAbstractServiceFactory::class,
            //MiddlewareAbstractServiceFactory::class,
        ],
        'alias' => [],
        'factories' => [],
    ],
];
