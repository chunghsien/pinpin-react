<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'np_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'where' => [
            [
                'isNull',
                'and',
                [
                    $PT . 'np_class.deleted_at'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'np_class.language_id',
                    new Expression(':language_id'),
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'np_class.locale_id',
                    new Expression(':locale_id'),
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'np_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'np_class_has_products',
            $PT.'np_class.id='.$PT.'np_class_has_products.np_class_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'np_class_has_products.products_id',
                    new Expression(':products_id'),
                ]
            ],
            
        ],
    ],
];