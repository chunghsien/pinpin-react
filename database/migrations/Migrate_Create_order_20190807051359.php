<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Ddl\Column\Decimal;

class Migrate_Create_order_20190807051359 extends AbstractMigration
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
    protected $table = 'order';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('member_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('logistics_global_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('serial', 'varchar', ['length' => 20]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('invoice_no', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('business_no', 'varchar', ['length' => 64, 'nullable' => true, 'comment' => "統一編號"]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('business_title', 'varchar', ['length' => 64, 'nullable' => true, 'comment' => "統編抬頭"]));

        $ddl->addColumn(new Decimal('subtotal', 16, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('trad_fee', 10, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('discount', 10, 4, false, '0.0000'));
        $ddl->addColumn(new Decimal('total', 16, 4, false, '0.0000'));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('first_name', 'varbinary', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('last_name', 'varbinary', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('fullname', 'varbinary', ['length' => 127, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('email', 'varbinary', ['length' => 384, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('phone', 'varbinary', ['length' => 32, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('cellphone', 'varbinary', ['length' => 32, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('country', 'varchar', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('state', 'varchar', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('county', 'varchar', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('district', 'varchar', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('zipcode', 'varchar', ['length' => 10, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('address', 'varbinary', ['length' => 255, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('tracking', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('pay_method', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('tail_number', 'varchar', ['length' => 20, 'nullable' => true]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('credit_sales_acount', 'varchar', ['length' => 64, 'nullable' => true]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('trad_sales_acount', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('message', 'varchar', ['length' => 255, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('status', 'tinyint', ['default' => 0]));
        //$ddl->addColumn(MySQLColumnFactory::buildColumn('reverse_status', 'tinyint', ['default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('shipmented_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $fkprefix = 'fk_'.$this->tailTable;
        $ddl->addConstraint(new Index('logistics_global_id'));
        $ddl->addConstraint(new Index('member_id', $this->table . '_idx_member_id'));
        $ddl->addConstraint(new Index('locale_id', $this->table . '_idx_locale_id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
