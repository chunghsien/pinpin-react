<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'products',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'model', 'alias', 'is_new', 'is_hot', 'is_show', 'viewed_count', 'sort', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'products.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'products2' => $PT . 'products'],
                $PT.'language_has_locale.language_id='.$PT.'products.language_id',
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
                    $PT . 'products.deleted_at',
                ]
            ],
        ],
    ],
];
