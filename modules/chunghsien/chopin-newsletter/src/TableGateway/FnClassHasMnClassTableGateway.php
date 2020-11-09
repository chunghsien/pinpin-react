<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class FnClassHasMnClassTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = true;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'fn_class_has_mn_class';
}
