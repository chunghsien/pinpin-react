<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class FacebookTagsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'facebook_tags';
}
