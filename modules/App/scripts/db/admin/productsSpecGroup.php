<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'products_spec_group',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'name', 'sort', 'created_at']
        ],
        'join' => [
            [
                $PT. 'products',
                $PT.'products_spec_group.products_id=products.id',
                ['model']
            ],
            [
                $PT . 'language_has_locale',
                $PT . 'products_spec_group.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'products_spec_group2' => $PT . 'products_spec_group'],
                $PT.'language_has_locale.language_id=products_spec_group2.language_id',
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
                    $PT . 'products_spec_group.deleted_at',
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
