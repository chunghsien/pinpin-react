<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'news',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'title', 'content', 'publish', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'news.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'news2' => $PT . 'news'],
                $PT.'language_has_locale.language_id=news2.language_id',
                []
            ],
        ],
        //$select->where->isNull($identifier)
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'language_has_locale.is_use',1
                ]
            ],
            [
                'isNull',
                'and',
                [
                    $PT . 'news.deleted_at',
                ]
            ],
        ],
    ],
];
