<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'system_settings',
        'quantifier' => 'distinct',
        'join' => [
            [
                $PT . 'language_has_locale',
                $PT . 'system_settings.locale_id=' . $PT . 'language_has_locale.locale_id',
                [
                    'code',
                    'display_name',
                    'is_use'
                ]
            ],
            [
                [ 'system_settings2' => $PT . 'system_settings'],
                $PT.'language_has_locale.language_id='.$PT.'system_settings.language_id',
                []
            ],
        ],
        'where' => [
            [
                'expression',
                'and',
                [
                    $PT.'system_settings.parent_id IN(?)'
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'language_has_locale.is_use',1
                ]
            ],
            [
                'isNull',
                'and',
                [
                    $PT . 'system_settings.deleted_at',
                ]
            ],
        ],
        
    ]
    
];
