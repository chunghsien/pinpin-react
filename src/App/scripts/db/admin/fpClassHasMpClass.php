<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'fp_class',
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
                    $PT . 'fp_class.deleted_at'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'fp_class.language_id',
                    new Expression(':language_id'),
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'fp_class.locale_id',
                    new Expression(':locale_id'),
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'fp_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'fp_class_has_mp_class',
            $PT.'fp_class.id='.$PT.'fp_class_has_mp_class.fp_class_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'fp_class_has_mp_class.mp_class_id',
                    new Expression(':mp_class_id'),
                ]
            ],
            
        ],
    ],
];