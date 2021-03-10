<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'banner',
        'quantifier' => 'distinct',
        'join' => [
            [
                "{$PT}language_has_locale",
                "{$PT}banner.locale_id={$PT}language_has_locale.locale_id",
                [
                    'code', 'display_name'
                ]
            ],
            [
                [ 'banner2' => "{$PT}banner"],
                "{$PT}language_has_locale.language_id=banner.language_id",
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
                'AND',
                ["{$PT}banner.deleted_at"]
            ],
        ],
    ],
];
