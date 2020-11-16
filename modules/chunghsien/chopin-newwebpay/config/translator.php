<?php

return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'phpArray',
                'base_dir' => dirname(__DIR__).'/resources/languages/',
                'pattern' => '%s/logistics.php',
                'text_domain' => 'newwebpay',
            ],
        ],
    ],
];
