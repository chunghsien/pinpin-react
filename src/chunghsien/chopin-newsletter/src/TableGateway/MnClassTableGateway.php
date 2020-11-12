<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class MnClassTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'mn_class';
}
