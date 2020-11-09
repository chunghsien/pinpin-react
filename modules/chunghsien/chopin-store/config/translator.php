<?php
return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'phpArray',
                'base_dir' => dirname(__DIR__).'/resources/languages/',
                'pattern' => '%s/chopin-store.php',
                'text_domain' => 'chopin-store',
            ],
        ],
    ],
];
