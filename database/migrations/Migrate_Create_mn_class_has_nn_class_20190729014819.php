<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_mn_class_has_nn_class_20190729014819 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'create';


    /**
     *
     * @var string
     * @desc middle_news_class_has_near_news_class
     */
    protected $table = 'mn_class_has_nn_class';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mn_class_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('nn_class_id', 'int', ['unsigned' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey(['mn_class_id', 'nn_class_id'], $this->tailTable.'_PRIMARY'));
        $fkprefix = 'fk_'.$this->tailTable;
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_mn_class', 'mn_class_id', self::$prefixTable.'mn_class', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_nn_class', 'nn_class_id', self::$prefixTable.'nn_class', 'id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
