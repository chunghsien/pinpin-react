<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'attributes',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'parent_id', 'language_id', 'locale_id', 'name', 'value', 'photo', 'sort', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'attributes.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'attributes2' => $PT . 'attributes'],
                $PT.'language_has_locale.language_id=attributes.language_id',
                []
            ],
        ],
        //$select->where->isNull($identifier)
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'attributes.parent_id', 0
                ]
            ],
            [
                'like',
                'and',
                [
                    $PT . 'attributes.table', '%products'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'attributes.table_id', 0
                ]
            ],
            [
                'isNull',
                'and',
                [
                    $PT . 'attributes.deleted_at',
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'language_has_locale.is_use',1
                ]
            ],
        ],
    ],
];
