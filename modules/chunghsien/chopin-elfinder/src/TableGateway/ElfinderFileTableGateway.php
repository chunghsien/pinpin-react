<?php

namespace Chopin\Elfinder\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class ElfinderFileTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'elfinder_file';
}
