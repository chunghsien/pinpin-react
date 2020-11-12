<?php

namespace Chopin\Users\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class UsersHasRolesTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'users_has_roles';
}
