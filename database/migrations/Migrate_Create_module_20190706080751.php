<?php
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_module_20190706080751 extends AbstractMigration
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
    protected $table = 'module';

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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', [
            'length' => 64,
            '',
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\UniqueKey('name', $this->tailTable . '_name_Unique'));
        $this->runSeed();
    }

    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Module_Insert.php';
        require $filepath;
        $seed = new \Module_Insert($this->adapter);
        $this->seed = $seed;
        //$seed->run();
    }


    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
