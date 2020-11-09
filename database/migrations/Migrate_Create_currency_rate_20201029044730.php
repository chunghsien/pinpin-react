<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;
use Laminas\Db\Sql\Ddl\Column\Decimal;
use Laminas\Db\Sql\Ddl\Constraint;

class Migrate_Create_currency_rate_20201029044730 extends AbstractMigration
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
    protected $table = 'currency_rate';
    
    protected $priority = 2;
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('main_currencies_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('rate_currencies_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(new Decimal('rate', 14, 4, false, 0));
        $ddl->addConstraint(new Index(['main_currencies_id'], $this->tailTable.'_main_currencies_id'));
        $ddl->addConstraint(new Index(['rate_currencies_id'], $this->tailTable.'_rate_currencies_id'));
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
