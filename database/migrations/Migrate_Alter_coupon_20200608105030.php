<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\Store\TableGateway\CouponTableGateway;
use Chopin\Store\CouponRule\FreeShippingCouponRule;

class Migrate_Alter_coupon_20200608105030 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'alter';

    /**
     *
     * @var string
     */
    protected $table = 'coupon';

    protected $priority = 3;

    public function up()
    {
        $tableGateway = new CouponTableGateway($this->adapter);
        $set = [
            'name' => '滿額免運',
            'language_id' => 119,
            'locale_id' => 229,
            'code' => crc32('default_target_amount_free_shipping'),
            'use_type' => 'rule_object',
            'rule_object' => FreeShippingCouponRule::class,
            'limit_type' => 'all_member',
            'start' => date("Y-m-d H:i:s"),
            'expiration' => '2199-12-31 23:59:59',
            'is_notremove' => 1,
            'is_use' => 1,
            'target_value' => '2000.00',
        ];
        if ($tableGateway->select($set)->count() == 0) {
            $tableGateway->insert($set);
        }
    }

    public function down()
    {
        ///
    }
}
