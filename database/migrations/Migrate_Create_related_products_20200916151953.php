<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_related_products_20200916151953 extends AbstractMigration
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
    protected $table = 'related_products';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('related_id', 'int', ['unsigned' => true]));
        $ddl->addConstraint(new Constraint\PrimaryKey(['products_id', 'related_id'], $this->tailTable . '_id_PRIMARY'));
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
