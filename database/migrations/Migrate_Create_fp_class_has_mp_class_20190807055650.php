<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_fp_class_has_mp_class_20190807055650 extends AbstractMigration
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
    protected $table = 'fp_class_has_mp_class';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('fp_class_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mp_class_id', 'int', ['unsigned' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey(['fp_class_id', 'mp_class_id'], $this->tailTable.'_PRIMARY_KEY'));
        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk_fp_class_has_mp_class_fp_class',
            'fp_class_id',
            self::$prefixTable.'fp_class',
            'id'
        ));

        $ddl->addConstraint(new Constraint\ForeignKey(
            'fp_class_has_mp_class_mp_class',
            'mp_class_id',
            self::$prefixTable.'mp_class',
            'id'
        ));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
