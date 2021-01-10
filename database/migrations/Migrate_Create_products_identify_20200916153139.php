<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Expression;

//產品識別
class Migrate_Create_products_identify_20200916153139 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'create';
    
    protected $priority = 2;
    
    /**
     * 
     * @var string
     */
    protected $table = 'products_identify';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sku', 'varchar', ['length' => 48, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('upc', 'varchar', ['length' => 10, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('ean', 'varchar', ['length' => 24, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('jan', 'varchar', ['length' => 24, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('isbn', 'varchar', ['length' => 24, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mpn', 'varchar', ['length' => 96, 'nullable' => true]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Index(['sku'], $this->table.'_idx_sku'));
        $ddl->addConstraint(new Index(['upc'], $this->table.'_idx_upc'));
        $ddl->addConstraint(new Index(['ean'], $this->table.'_idx_ean'));
        $ddl->addConstraint(new Index(['jan'], $this->table.'_idx_jan'));
        $ddl->addConstraint(new Index(['isbn'], $this->table.'_idx_isbn'));
        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk1_'.$this->tailTable,
            'products_id',
            self::$prefixTable.'products',
            'id'
        ));
        
        
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
