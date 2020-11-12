<?php

use Laminas\Db\Sql\Expression;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

$PT = AbstractTableGateway::$prefixTable;

return [
    'today_registed' => [
        'from' => $PT.'member',
        'columns' => [ [new Expression('COUNT(`'.$PT.'member`.`id`) as _count')] ],
        'where' => [
            ['isNull', 'and', [$PT.'member.deleted_at']],
            ['greaterThanOrEqualTo', 'and', [$PT.'member.created_at', date("Y-m-d 00:00:00")]]
        ],
    ],
    'total_registed' => [
        'from' => $PT.'member',
        'columns' => [ [new Expression('COUNT(`'.$PT.'member`.`id`) as _count')] ],
        'where' => [
            ['isNull', 'and', [$PT.'member.deleted_at']],
        ],
    ],
    'today_ordered' => [
        'from' => $PT.'order',
        'columns' => [ [new Expression('COUNT(`id`) as _count')] ],
        'where' => [
            ['isNull', 'and', ['deleted_at']],
            ['greaterThanOrEqualTo', 'and', ['created_at', date("Y-m-d 00:00:00")]]
        ],
        
    ],
    'total_ordered' => [
        'from' => $PT.'order',
        'columns' => [ [new Expression('COUNT(`id`) as _count')] ],
        'where' => [
            ['isNull', 'and', ['deleted_at']],
        ],
        
    ],
    
];