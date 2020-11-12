<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'products_spec',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'name', 'main_photo', 'stock', 'price', 'real_price', 'stock_status', 'sort', 'created_at']
        ],
        'join' => [
            [
                $PT. 'products',
                $PT.'products_spec.products_id=products.id',
                ['model'],
                Select::JOIN_LEFT,
            ],
            [
                $PT. 'products_spec_group',
                $PT.'products_spec.products_spec_group_id=products_spec_group.id',
                ['group_name' => 'name'],
                Select::JOIN_LEFT,
            ],
        ],
        'where' => [
            [
                'isNull',
                'and',
                [$PT . 'products_spec.deleted_at']
            ],
        ],
    ],
];
