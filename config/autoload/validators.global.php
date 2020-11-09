<?php

use Chopin\Validator\Db\CrcDataExists;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'validators' => [
        'alias' => [
            'crcdataexists'         => CrcDataExists::class,
            'crcDataExists'         => CrcDataExists::class,
            'CrcDataExists'         => CrcDataExists::class,
        ],
        'factories' => [
            CrcDataExists::class => InvokableFactory::class,
        ],
    ],
];
