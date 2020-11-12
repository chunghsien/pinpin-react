<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class AttributesTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'attributes';
}
