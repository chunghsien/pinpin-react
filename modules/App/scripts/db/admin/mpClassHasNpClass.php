<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'mp_class',
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
                    $PT . 'mp_class.deleted_at'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'mp_class.language_id',
                    new Expression(':language_id'),
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'mp_class.locale_id',
                    new Expression(':locale_id'),
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'mp_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'mp_class_has_np_class',
            $PT.'mp_class.id='.$PT.'mp_class_has_np_class.mp_class_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'mp_class_has_np_class.np_class_id',
                    new Expression(':np_class_id'),
                ]
            ],
            
        ],
    ],
];