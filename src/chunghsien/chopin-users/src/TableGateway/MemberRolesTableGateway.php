<?php

namespace Chopin\Users\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class MemberRolesTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'member_roles';

}
