<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;
return [
    'options' => [
        'from' => $PT . 'roles',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'where' => [
            [
                'notEqualTo',
                'AND',
                ['name', 'administrator']
            ],
            [
                'equalTo',
                'AND',
                ['module_id', 2]
            ],
            [
                'greaterThan',
                'AND',
                ['parent_id', 0]
            ],
            [
                'greaterThan',
                'AND',
                ['depth', 0]
            ],
            [
                'isNull',
                'and',
                [
                    $PT . 'roles.deleted_at'
                ]
            ],
        ]
    ],
    'defaultValue' => [
        'from' => $PT . 'roles',
        'columns' => [
            [
                'value' => 'id',
                'label' => 'name',
            ]
        ],
        'join' => [
            $PT.'users_has_roles',
            $PT.'roles.id='.$PT.'users_has_roles.roles_id',
            []
        ],
        'where' => [
            [
                'equalTo',
                'and',
                [
                    $PT . 'users_has_roles.users_id',
                    new Expression(':users_id'),
                ]
            ],
            
        ],
    ],
];