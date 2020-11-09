<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_roles_20190606135005 extends AbstractMigration
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
    protected $table = 'roles';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('module_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('parent_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('depth', 'smallint', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 64]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\UniqueKey('name', $this->tailTable.'_name_Unique'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk_roles_module1', 'module_id', self::$prefixTable.'module', 'id'));
        $this->runSeed();
    }

    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Chopin_Users_Roles_Insert.php';
        require $filepath;
        $seed = new \Chopin_Users_Roles_Insert($this->adapter);
        $this->seed = $seed;
        //$seed->run();
    }



    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
