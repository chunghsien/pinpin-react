<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
//use Laminas\Db\Sql\Select;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'roles',
        'quantifier' => 'distinct',
        'columns' => [
            ['id', 'name', 'created_at']
        ],
        //$select->where->isNull($identifier)
        'where' => [
            ['equalTo', 'and', ['module_id', 2]],
            ['notEqualTo', 'and', ['name', 'administrator']],
            ['isNull', 'and', ['deleted_at',]],
        ],
    ],
];