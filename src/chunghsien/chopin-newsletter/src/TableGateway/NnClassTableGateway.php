<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class NnClassTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'nn_class';
}
