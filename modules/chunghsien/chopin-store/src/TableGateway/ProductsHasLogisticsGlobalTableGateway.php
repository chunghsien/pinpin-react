<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class ProductsHasLogisticsGlobalTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = true;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'products_has_logistics_global';
}
