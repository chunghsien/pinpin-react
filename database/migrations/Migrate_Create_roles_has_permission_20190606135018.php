<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_roles_has_permission_20190606135018 extends AbstractMigration
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
    protected $table = 'roles_has_permission';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('roles_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('permission_id', 'int', ['unsigned' => true]));
        $ddl->addConstraint(
            new Constraint\ForeignKey(
                'fk_'.$this->tailTable.'_roles',
                'roles_id',
                self::$prefixTable.'roles',
                'id'
            )
        );

        $ddl->addConstraint(
            new Constraint\ForeignKey(
                'fk_'.$this->tailTable.'_permission',
                'permission_id',
                self::$prefixTable.'permission',
                'id'
            )
        );
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
