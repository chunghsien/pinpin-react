<?php

namespace Chopin\SystemSettings\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class ModuleTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'module';
}
