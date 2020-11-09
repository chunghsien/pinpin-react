<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class FpClassHasMpClassTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = true;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'fp_class_has_mp_class';
}
