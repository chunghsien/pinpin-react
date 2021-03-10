<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Migrate_Alter_system_settings_20210112092436 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'alter';
    
    
    /**
     * 
     * @var string
     */
    protected $table = 'system_settings';
    
    protected $priority = 3;
    
    public function up()
    {
        $tablegateway = new SystemSettingsTableGateway($this->adapter);
        $set = [
            'parent_id' => 0,
            'key' => 'system-maintain',
            'name' => '系統維護狀態',
            'value' => 0,
            'deleted_at' => date("Y-m-d H:i:s"),
        ];
        $tablegateway->insert($set);
    }
    
    public function down()
    {
        //
    }
}
