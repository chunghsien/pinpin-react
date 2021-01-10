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
        /*
        'lexer' => [
            //'tag_comment' => ['{#', '#}'],
            'tag_comment' => ['[#', '#]'],
            //'tag_block' => ['{%', '%}'],
            'tag_block' => ['[%', '%]'],
            //'tag_variable' => ['{{', '}}'],
            'tag_variable' => ['[?', '?]'],
            //'whitespace_trim' => '-',
            //'whitespace_line_trim' => '~',
            //'whitespace_line_chars' => ' \t\0\x0B',
            //'interpolation' => ['#{', '}'],
        ],
        */
        'autoescape' => 'html',
        'cache_dir' => APP_ENV === 'production' ? 'storage/cache/twig' : false,
        'debug' => APP_ENV != 'production',
        'timezone' => "Asia/Taipei",
        'auto_reload' => APP_ENV !== 'production',
        'optimizations' => APP_ENV == 'production' ? OptimizerNodeVisitor::OPTIMIZE_ALL : OptimizerNodeVisitor::OPTIMIZE_NONE,
    ],
];
