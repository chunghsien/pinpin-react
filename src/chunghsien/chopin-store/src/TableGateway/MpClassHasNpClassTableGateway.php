<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class MpClassHasNpClassTableGateway extends AbstractTableGateway
{
    public static $isRemoveRowGatewayFeature = true;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'mp_class_has_np_class';
}
