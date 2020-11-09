<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Column\Decimal;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_order_detail_20190807062334 extends AbstractMigration
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
    protected $table = 'order_detail';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('order_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('model', 'varchar', ['length' => 128]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('quantity', 'mediumint', ['unsigned' => true, 'default' => 0]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('status', 'tinyint', ['default' => 0]));

        $ddl->addColumn(new Decimal('price', 16, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('subtotal', 16, 4, false, '0.0000'));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
            'on_update' => true,
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk_order_detail_order1_idx', 'order_id', self::$prefixTable.'order', 'id'));
        $ddl->addConstraint(new Index('products_id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
