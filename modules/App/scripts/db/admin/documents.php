<?php
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
$PT = AbstractTableGateway::$prefixTable;

return [
    'pagiantor' => [
        'from' => $PT . 'documents',
        'quantifier' => 'distinct',
        'join' => [
            [
                "{$PT}language_has_locale",
                "{$PT}documents.locale_id={$PT}language_has_locale.locale_id",
                [
                    'code',
                    'display_name'
                ]
            ],
            [
                [
                    'documents2' => "{$PT}documents"
                ],
                "{$PT}language_has_locale.language_id=documents2.language_id",
                []
            ]
        ],
        // $select->where->isNull($identifier)
        'where' => [
            [
                'isNull',
                'AND',
                [
                    "{$PT}documents.deleted_at"
                ]
            ],
            [
                'equalTo',
                'and',
                [
                    $PT . 'language_has_locale.is_use',
                    1
                ]
            ],
            [
                'in',
                'and',
                [
                    "{$PT}documents.type",
                    [
                        1,
                        2
                    ]
                ]
            ]
        ],
        'order' => [
            'id desc'
        ]
    ]
];
