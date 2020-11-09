<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class SeoTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'seo';
}
