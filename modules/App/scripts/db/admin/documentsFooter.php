<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => "{$PT}layout_zones",
        'quantifier' => 'distinct',
        'columns' => [
            [
                'id',
                'language_id',
                'locale_id',
                'name',
                'created_at'
            ]
        ],
        'join' => [
            [
                "{$PT}language_has_locale",
                "{$PT}layout_zones.locale_id={$PT}language_has_locale.locale_id",
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [
                    'layout_zones2' => "{$PT}layout_zones"
                ],
                "{$PT}language_has_locale.language_id=layout_zones2.language_id",
                []
            ],
        ],
        // $select->where->isNull($identifier)
        'where' => [
            [
                'equalTo',
                'and',
                [
                    "{$PT}language_has_locale.is_use",
                    1
                ]
            ],
        ],
    ],
];
