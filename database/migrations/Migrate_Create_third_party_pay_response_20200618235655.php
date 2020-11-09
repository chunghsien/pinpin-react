<?php
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Ddl\Column\Text;
use Laminas\Db\Sql\Expression;

class Migrate_Create_third_party_pay_response_20200618235655 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    protected $priority = 2;

    /**
     *
     * @var string
     */
    protected $table = 'third_party_pay_response';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('order_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(new Text('response'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('status', 'varchar', ['length' => 20, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('message', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', [
            'nullable' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
            'on_update' => true,
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $pt = AbstractTableGateway::$prefixTable;
        $ddl->addConstraint(new Constraint\ForeignKey('fk_order_001', 'order_id', $pt.'order', 'id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
