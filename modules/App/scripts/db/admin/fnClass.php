<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'fn_class',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'name', 'viewed_count', 'sort', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'fn_class.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'fn_class2' => $PT . 'fn_class'],
                $PT.'language_has_locale.language_id=fn_class2.language_id',
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
                    $PT . 'fn_class.deleted_at',
                ]
            ],
        ],
    ],
];
