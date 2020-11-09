<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Ddl\Column\Decimal;

class Migrate_Create_logistics_global_20200606215955 extends AbstractMigration
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
    protected $table = 'logistics_global';

    protected $priority = 1;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', [
            'unsigned' => true,
            'auto_increment' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', [
            'unsigned' => true,
            'default' => 0,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', [
            'unsigned' => true,
            'default' => 0,
        ]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('manufacturer', 'varchar', ['length' => 48]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('method', 'varchar', ['length' => 48]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 48]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('code', 'varchar', ['length' => 48]));

        $ddl->addColumn(new Decimal('price', 8, 2));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_use', 'tinyint', [
            'unsigned' => true,
            'default' => 1,
            'length' => 1,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('param', 'varchar', ['length' => 64]));
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
        $ddl->addConstraint(new Index('language_id', $this->table . '_idx_language_id'));
        $ddl->addConstraint(new Index('manufacturer', $this->table . '_idx_manufacturer01'));
        $ddl->addConstraint(new Index('method', $this->table . '_idx_method01'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
