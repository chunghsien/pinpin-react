<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_nn_class_has_news_20190729014903 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'create';


    /**
     *
     * @var string
     * @desc near_news_class_has_news
     */
    protected $table = 'nn_class_has_news';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('nn_class_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('news_id', 'int', ['unsigned' => true]));
        $ddl->addConstraint(new Constraint\PrimaryKey(['news_id', 'nn_class_id'], $this->tailTable.'_PRIMARY'));
        $fkprefix = 'fk_'.$this->tailTable;
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_nn_class', 'nn_class_id', self::$prefixTable.'nn_class', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_news', 'news_id', self::$prefixTable.'news', 'id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
