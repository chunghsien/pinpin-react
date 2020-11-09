<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'fn_class',
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
                    $PT . 'fn_class.deleted_at'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'fn_class.language_id',
                    new Expression(':language_id'),
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'fn_class.locale_id',
                    new Expression(':locale_id'),
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'fn_class',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'fn_class_has_mn_class',
            $PT.'fn_class.id='.$PT.'fn_class_has_mn_class.fn_class_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'fn_class_has_mn_class.mn_class_id',
                    new Expression(':mn_class_id'),
                ]
            ],
            
        ],
    ],
];