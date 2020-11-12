<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'member_roles',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'language_id', 'locale_id', 'name', 'created_at']
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'member_roles.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'member_roles2' => $PT . 'member_roles'],
                $PT.'language_has_locale.language_id=member_roles2.language_id',
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
                    $PT . 'member_roles.deleted_at',
                ]
            ],
        ],
    ],
];
