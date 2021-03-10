<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_products_spec_group_20200918070504 extends AbstractMigration
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
    protected $table = 'products_spec_group';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_spec_group_attrs_id', 'int', ['unsigned' => true]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true, 'default' => 0]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true, 'default' => 0]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 128, 'nullable' => true]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('extra_name', 'varchar', ['length' => 128, 'nullable' => true]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('image', 'varchar', ['length' => 384, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sale_count', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $prefixTable = self::$prefixTable;
        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk_roducts_spec_group_products',
            'products_id',
            "{$prefixTable}products",
            'id'
        ));
        
        $ddl->addConstraint(new Index(['products_spec_group_attrs_id'], $this->table.'_idx_products_spec_group_attrs_id'));
        $ddl->addConstraint(new Index(['sort'], $this->table.'_idx_sort'));
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
