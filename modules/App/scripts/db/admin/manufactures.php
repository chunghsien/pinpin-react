<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'manufactures',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'name', 'sort',  'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'manufactures.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'manufactures2' => $PT . 'manufactures'],
                $PT.'language_has_locale.language_id=manufactures2.language_id',
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
                    $PT . 'manufactures.deleted_at',
                ]
            ],
        ],
    ],
];
