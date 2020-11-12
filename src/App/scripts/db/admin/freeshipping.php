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
                //過濾掉滿額免運
                'equalTo',
                'AND',
                ["{$PT}coupon.rule_object", FreeShippingCouponRule::class]
            ],
            [
                'isNull',
                'and',
                [
                    "{$PT}coupon.deleted_at",
                ]
            ],
        ],
    ],
];
