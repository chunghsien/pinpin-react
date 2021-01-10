<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Column\Decimal;
use Laminas\Db\Sql\Expression;

class Migrate_Create_products_volume_20200916153216 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    protected $priority = 3;
    
    /**
     *
     * @var string
     */
    protected $table = 'products_volume';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', [
            'unsigned' => true,
            'auto_increment' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', [
            'unsigned' => true
        ]));
        
        $ddl->addColumn(new Decimal('width', 12, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('height', 12, 4, false, '0.0000'));
        
        //卡在javascript HTMLFormControlsCollection，length關鍵字所以替換同義單字
        $ddl->addColumn(new Decimal('distance', 12, 4, false, '0.0000'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('dimensions_unit', 'varchar', ['length' => 24, 'nullable' => true]));
        $ddl->addColumn(new Decimal('weight', 12, 4, false, '0.0000'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('weight_unit', 'varchar', ['length' => 24, 'nullable' => true]));
        $ddl->addColumn(new Decimal('volume', 12, 4, false, '0.0000'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('volume_unit', 'varchar', ['length' => 24, 'nullable' => true]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk1_' . $this->tailTable, 'products_id', self::$prefixTable . 'products', 'id'
            ));
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
