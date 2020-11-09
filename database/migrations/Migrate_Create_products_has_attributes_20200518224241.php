<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_products_has_attributes_20200518224241 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    protected $priority = 2;

    /**
     *
     * @var string
     */
    protected $table = 'products_has_attributes';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('attributes_id', 'int', [
            'unsigned' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', [
            'unsigned' => true
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey([
            'attributes_id',
            'products_id'
        ], $this->tailTable . '_id_PRIMARY'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
