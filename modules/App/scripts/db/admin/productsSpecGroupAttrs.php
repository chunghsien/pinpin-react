<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'products_spec_group_attrs',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'name', 'extra_name', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'products_spec_group_attrs.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'products_spec_group_attrs2' => $PT . 'products_spec_group_attrs'],
                $PT.'language_has_locale.language_id=products_spec_group_attrs2.language_id',
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
                    $PT . 'products_spec_group_attrs.deleted_at',
                ]
            ],
        ],
    ],
];
