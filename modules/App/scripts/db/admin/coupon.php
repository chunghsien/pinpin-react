<?php

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\Store\CouponRule\FreeShippingCouponRule;

$PT = AbstractTableGateway::$prefixTable;
return [
    'pagiantor' => [
        'from' => "{$PT}coupon",
        'quantifier' => 'distinct',
        'join' => [
            [
                "{$PT}language_has_locale",
                "{$PT}coupon.locale_id={$PT}language_has_locale.locale_id",
                [
                    'display_name',
                ]
            ],
            [
                [ 'coupon2' => "{$PT}coupon"],
                $PT.'language_has_locale.language_id=coupon2.language_id',
                []
            ],
        ],
        //$select->where->isNull($identifier)
        'where' => [
            [
                'equalTo',
                'and',
                [
                    "{$PT}language_has_locale.is_use", 1
                ]
            ],
            [
                'isNull',
                'and',
                [
                    "{$PT}coupon.deleted_at",
                ]
            ],
            [
                //過濾掉滿額免運
                'notEqualTo',
                'AND',
                [
                    "{$PT}coupon.rule_object",
                    FreeShippingCouponRule::class
                ]
            ]
        ],
    ],
];
