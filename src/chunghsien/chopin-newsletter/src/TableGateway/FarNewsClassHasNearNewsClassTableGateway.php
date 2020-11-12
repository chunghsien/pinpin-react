<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class FarNewsClassHasNearNewsClassTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = true;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'far_news_class_has_near_news_class';
}
