<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'options' => [
        'from' => $PT . 'permission',
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
                    $PT . 'permission.deleted_at'
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'permission',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'roles_has_permission',
            $PT.'permission.id='.$PT.'roles_has_permission.permission_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'roles_has_permission.roles_id',
                    new Expression(':roles_id'),
                ]
            ],
            
        ],
    ],
];