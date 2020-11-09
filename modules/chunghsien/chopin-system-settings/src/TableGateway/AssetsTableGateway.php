<?php

namespace Chopin\SystemSettings\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class AssetsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'assets';
}
