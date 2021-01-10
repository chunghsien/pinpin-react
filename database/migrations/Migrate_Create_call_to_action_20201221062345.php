<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_call_to_action_20201221062345 extends AbstractMigration
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
    protected $table = 'call_to_action';

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
            'auto_increment' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table', 'varchar', [
            'length' => 64,
            'nullable' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table_id', 'int', [
            'unsigned' => true,
            'default' => 0
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('title', 'varchar', [
            'length' => 96,
            'nullable' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('tags', 'varchar', [
            'length' => 255,
            'nullable' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('image', 'varchar', [
            'length' => 384,
            'nullable' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('target', 'varchar', [
            'length' => 10,
            'default' => 'self'
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('url', 'varchar', [
            'length' => 384,
            'nullable' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', [
            'default' => new Expression('CURRENT_TIMESTAMP')
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', [
            'default' => new Expression('CURRENT_TIMESTAMP'),
            'on_update' => true
        ]));
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $this->runSeed();
    }
    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Chopin_Call_To_Action.php';
        require $filepath;
        $seed = new \Chopin_Call_To_Action($this->adapter);
        $this->seed = $seed;
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
