<?php

use Chopin\Elfinder\ElfinderConnectorFactory;
use Chopin\Elfinder\Http\Action;

return [
    'dependencies' => [
        'factories' => [
            elFinderConnector::class => ElfinderConnectorFactory::class,
            Action\ElfinderConnectorAction::class => Action\ElfinderConnectorActionFactory::class,
        ],
    ],
];
