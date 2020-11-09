<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Ddl\Column\Decimal;

class Migrate_Create_coupon_20200606220055 extends AbstractMigration
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
    protected $table = 'coupon';

    protected $priority = 1;

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

        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 32]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('code', 'varchar', ['length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('use_type', 'varchar', ['length' => 32]));
        $ddl->addColumn(new Decimal('target_value', 12, 2, false, -1));
        $ddl->addColumn(new Decimal('use_value', 12, 2, false, 0));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('rule_object', 'varchar', ['length' => 255, 'nullable' => true, 'comments' => '使用php class達到功能']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('limit_type', 'varchar', ['length' => 32]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_notremove', 'tinyint', ['length' => 1, 'unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_use', 'tinyint', ['length' => 1, 'unsigned' => true, 'default' => 0]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('start', 'datetime', ['comments' => '開始日期', ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('expiration', 'datetime', ['comments' => '結束時間', ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Index('start', $this->table . '_idx_start'));
        $ddl->addConstraint(new Index('expiration', $this->table . '_idx_expiration'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
