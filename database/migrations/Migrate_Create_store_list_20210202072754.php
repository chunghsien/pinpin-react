<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_store_list_20210202072754 extends AbstractMigration
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
    protected $table = 'store_list';
    
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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 128]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('image', 'varchar', ['length' => 384]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('tel', 'varchar', ['length' => 32]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('store_address', 'varchar', ['length' => 384]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_wifi', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_park', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_credit', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
    }
    
    public function down()
    {
        //
    }
}
