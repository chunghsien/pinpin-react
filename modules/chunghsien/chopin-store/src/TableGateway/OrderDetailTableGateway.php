<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class OrderDetailTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'order_detail';
}
