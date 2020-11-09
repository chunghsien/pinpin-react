<?php

namespace Chopin\LaminasDb\TableGateway;

class MigrationsTableGateway extends AbstractTableGateway
{
    
    public static $isRemoveRowGatewayFeature = false;
    
    protected $table = 'migrations';

    protected $primary = [ 'id' ];
}
