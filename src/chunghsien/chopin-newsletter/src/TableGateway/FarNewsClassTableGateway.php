<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class FarNewsClassTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = true;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'far_news_class';
}
