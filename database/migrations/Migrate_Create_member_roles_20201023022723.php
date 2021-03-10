<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_member_roles_20201023022723 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'create';
    
    
    /**
     * 
     * @var string
     */
    protected $table = 'member_roles';
    
    protected $priority = 2;
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true, 'default' => 0]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn(
            'parent_id', 
            'int', 
            [
                'unsigned' => true,
                'default' => 0,
                'comments' => '一般都設0，有階層狀態到時候再設計(直銷)',
            ]
        ));
        $ddl->addColumn(MySQLColumnFactory::buildColumn(
            'depth',
            'smallint',
            [
                'unsigned' => true, 
                'default' => 0,
                'comments' => '一般都設0，有階層狀態到時候再設計(直銷)',
            ]
        ));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 48]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\UniqueKey('name', $this->tailTable.'_name_Unique'));
        $this->runSeed();
        
    }
    
    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Chopin_Member_Roles_Insert.php';
        require $filepath;
        $seed = new \Chopin_Member_Roles_Insert($this->adapter);
        $this->seed = $seed;
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
