<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'logistics_global',
        'quantifier' => 'distinct',
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'logistics_global.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'display_name',
                ]
            ],
            [
                [ 'logistics_global2' => $PT . 'logistics_global'],
                $PT.'language_has_locale.language_id='.$PT.'logistics_global.language_id',
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
                    $PT . 'logistics_global.deleted_at',
                ]
            ],
        ],
    ],
];
