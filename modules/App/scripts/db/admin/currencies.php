<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'currency_rate',
        'quantifier' => 'distinct',
        'join' => [
            [
                $PT . 'currencies',
                $PT . 'currency_rate.rate_currencies_id=' . $PT . 'currencies.id',
                [
                    'code', 'name'
                ]
            ],
            [
                [ 'main_currencies' => $PT . 'currencies'],
                $PT.'currency_rate.main_currencies_id='.$PT.'main_currencies.id',
                [
                    'main_code' => 'code',
                    'main_name' => 'name',
                ]
            ],
        ],
    ],
];
