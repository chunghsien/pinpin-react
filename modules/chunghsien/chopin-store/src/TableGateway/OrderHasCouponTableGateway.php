<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class OrderHasCouponTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = true;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'order_has_coupon';
}
