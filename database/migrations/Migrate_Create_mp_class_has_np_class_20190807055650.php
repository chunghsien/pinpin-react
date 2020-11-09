<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_mp_class_has_np_class_20190807055650 extends AbstractMigration
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
    protected $table = 'mp_class_has_np_class';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mp_class_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('np_class_id', 'int', ['unsigned' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey(['mp_class_id', 'np_class_id'], $this->tailTable.'_PRIMARY_KEY'));
        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk_mp_class_has_np_class_mp_class',
            'mp_class_id',
            self::$prefixTable.'mp_class',
            'id'
        ));

        $ddl->addConstraint(new Constraint\ForeignKey(
            'mp_class_has_np_class_np_class',
            'np_class_id',
            self::$prefixTable.'np_class',
            'id'
        ));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
