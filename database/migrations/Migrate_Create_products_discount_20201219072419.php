<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_products_discount_20201219072419 extends AbstractMigration
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
    protected $table = 'products_discount';
    
    protected $priority = 2;
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('discount', 'tinyint', ['unsigned' => true, 'default' => 100]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('start_date', 'datetime'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('end_date', 'datetime'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $fkprefix = 'fk_'.$this->tailTable;
        $pt = self::$prefixTable;
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_products', 'products_id', "{$pt}products", 'id'));
        
        
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
