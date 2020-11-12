<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class LogisticsGlobalTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'logistics_global';
    
    protected function isExists($set) {
        return $this->select($set)->count;
    }
}
