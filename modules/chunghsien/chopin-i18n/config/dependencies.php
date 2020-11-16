<?php

use Chopin\I18n\Translator\Translator;
use Chopin\I18n\Translator\TranslatorServiceFactory;

return
[
    'dependencies' => [
        'invokables' => [],
        'factories' => [
            Translator::class => TranslatorServiceFactory::class,
        ],
    ],
];
