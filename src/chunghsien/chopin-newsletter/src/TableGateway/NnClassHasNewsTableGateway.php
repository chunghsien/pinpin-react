<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class NnClassHasNewsTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = true;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'nn_class_has_news';
}
