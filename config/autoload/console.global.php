<?php
return [
    'console' => [
        'migrations' => [
            dirname(dirname(__DIR__)).'/database/migrations',
        ],
        'sql_seeds' => [
            dirname(dirname(__DIR__)).'/database/sql_seeds',
        ],
        'resources' => [dirname(__DIR__).'/resources'],
    ],

];
