<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_np_class_has_products_20190807055550 extends AbstractMigration
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
    protected $table = 'np_class_has_products';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('np_class_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey(['np_class_id', 'products_id'], $this->tailTable.'_PRIMARY_KEY'));
        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk_np_class_has_products_products1_idx',
            'products_id',
            self::$prefixTable.'products',
            'id'
        ));

        $ddl->addConstraint(new Constraint\ForeignKey(
            'fk_np_class_has_products_np_class1_idx',
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
