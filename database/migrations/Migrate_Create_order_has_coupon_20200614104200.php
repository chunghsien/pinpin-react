<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Expression;

class Migrate_Create_order_has_coupon_20200614104200 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    /**
     *
     * @var string
     */
    protected $table = 'order_has_coupon';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('order_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('coupon_id', 'int', ['unsigned' => true]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $fkprefix = 'fk_'.$this->tailTable;
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_order', 'order_id', self::$prefixTable.'order', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_coupon', 'coupon_id', self::$prefixTable.'coupon', 'id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
