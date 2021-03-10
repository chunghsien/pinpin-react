<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'products_spec',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', /*'name', 'main_photo',*/'stock', 'price', 'real_price', 'stock_status', 'sort', 'created_at']
        ],
        'join' => [
            [
                "{$PT}products",
                "{$PT}products_spec.products_id={$PT}products.id",
                ['model'],
                Select::JOIN_LEFT,
            ],
            [
                "{$PT}products_spec_group",
                "{$PT}products_spec.products_spec_group_id={$PT}products_spec_group.id",
                [],
                Select::JOIN_LEFT,
            ],
            [
                "{$PT}products_spec_group_attrs",
                "{$PT}products_spec_group.products_spec_group_attrs_id={$PT}products_spec_group_attrs.id",
                ["group_name" => "name"],
                Select::JOIN_LEFT,
            ],
            [
                $PT. 'products_spec_attrs',
                "{$PT}products_spec.products_spec_attrs_id=${PT}products_spec_attrs.id",
                ["name"],
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
