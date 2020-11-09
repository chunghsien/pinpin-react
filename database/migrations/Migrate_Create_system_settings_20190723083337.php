<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_system_settings_20190723083337 extends AbstractMigration
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
    protected $table = 'system_settings';

    protected $priority = 2;

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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('parent_id', 'int', [
            'unsigned' => true,
            'default' => 0,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('input_type', 'varchar', [
            'length' => 512,
            'nullable' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('key', 'varchar', [
            'length' => 64,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', [
            'length' => 64,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('value', 'varchar', [
            'length' => 2048,
            'nullable' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('aes_value', 'varbinary', [
            'length' => 2048,
            'nullable' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'smallint', [
            'unsigned' => true,
            'default' => 65535,
        ]));
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
        $ddl->addConstraint(new Index([
            'language_id',
            'locale_id',
        ], $this->tailTable . '_idx_language_locale'));
        // $ddl->addConstraint(new IndexConstraint('locale_id'));

        $this->runSeed();
    }

    public function runSeed()
    {
        $smtpSettingPath = dirname(__DIR__) . '/sql_seeds/Chopin_System_Settings_Insert.php';
        require $smtpSettingPath;
        $seed = new \Chopin_System_Settings_Insert($this->adapter);
        $this->seed = $seed;
        // $seed->run();
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
