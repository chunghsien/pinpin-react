<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'language',
        'columns' => [
            [
                'id' => new Expression('CONCAT(`'.$PT.'language`.`id`, "-", `'.$PT.'locale`.`id`)'),
            ]
        ],
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'language.id=' . $PT . 'language_has_locale.language_id',
                [
                    'language_id',
                    'locale_id',
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                $PT . 'locale',
                $PT . 'language_has_locale.locale_id=' . $PT . 'locale.id',
                []
            ],
        ],
        'where' => [
            [
                'isNull',
                'and',
                [
                    $PT . 'language.deleted_at'
                ]
            ],
            [
                'isNull',
                'and',
                [
                    $PT . 'locale.deleted_at'
                ]
            ],
            /*[
                'equalTo',
                'and',
                [
                    $PT . 'language_has_locale.is_use',
                    1
                ]
            ],*/
        ],
    ],
];
