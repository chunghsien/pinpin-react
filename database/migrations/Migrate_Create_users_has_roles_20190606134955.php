<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_users_has_roles_20190606134955 extends AbstractMigration
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
    protected $table = 'users_has_roles';

    protected $priority = 3;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('users_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('roles_id', 'int', ['unsigned' => true]));

        $ddl->addConstraint(new Constraint\ForeignKey('fk_' . $this->tailTable . '_users', 'users_id', self::$prefixTable.'users', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk_' . $this->tailTable . '_roles', 'roles_id', self::$prefixTable.'roles', 'id'));
        $ddl->addConstraint(new Constraint\PrimaryKey(['users_id', 'roles_id']));
        $this->runSeed();
    }

    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Chopin_Users_Users_Has_Roles_Insert.php';
        require $filepath;
        $seed = new \Chopin_Users_Users_Has_Roles_Insert($this->adapter);
        $this->seed = $seed;
        //$seed->run();
    }


    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
