<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Ddl\Column\Decimal;

class Migrate_Create_products_20190807045206 extends AbstractMigration
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
    protected $table = 'products';

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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('manufactures_id', 'int', ['unsigned' => true, 'default' => 0]));
        
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('model', 'varchar', ['length' => 128]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('alias', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('introduction', 'varchar', ['length' => 1024, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('detail', 'text'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('stock', 'mediumint', ['unsigned' => true, 'default' => 0]));

        $ddl->addColumn(new Decimal('price', 15, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('real_price', 15, 4, false, '0.0000'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('stock_status', 'tinyint', ['default' => -1]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_new', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_hot', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_show', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('viewed_count', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sale_count', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Index(['sort'], $this->tailTable.'_idx_sort'));
        $ddl->addConstraint(new Index(['is_new'], $this->tailTable.'_idx_is_new'));
        $ddl->addConstraint(new Index(['is_hot'], $this->tailTable.'_idx_is_hot'));
        $ddl->addConstraint(new Index(['manufactures_id'], $this->tailTable.'_idx_manufactures_id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
