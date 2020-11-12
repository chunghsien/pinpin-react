<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'mn_class',
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
                    $PT . 'mn_class.deleted_at'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'mn_class.language_id',
                    new Expression(':language_id'),
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'mn_class.locale_id',
                    new Expression(':locale_id'),
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'mn_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'mn_class_has_nn_class',
            $PT.'mn_class.id='.$PT.'mn_class_has_nn_class.mn_class_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'mn_class_has_nn_class.nn_class_id',
                    new Expression(':nn_class_id'),
                ]
            ],
            
        ],
    ],
];