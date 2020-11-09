<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Join;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'users',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'account', 'created_at']
        ],
        'join' => [
            [
                $PT . 'users_has_roles',
                $PT . 'users.id=' . $PT . 'users_has_roles.users_id',
                [],
                Join::JOIN_LEFT
            ],
            [
                $PT . 'roles',
                $PT . 'users_has_roles.roles_id=' . $PT . 'roles.id',
                [
                    'role_name' => 'name',
                ],
                Join::JOIN_LEFT
            ],
        ],
        'where' => [
            ['greaterThan', 'AND', [$PT.'users.parent_id', 0]],
            ['greaterThan', 'AND', [$PT.'users.depth', 0]],
            ['isNull', 'and', [$PT.'users.deleted_at',]],
        ],
    ],
];
