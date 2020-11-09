<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_tw_bank_list_20201103022657 extends AbstractMigration
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
    protected $table = 'tw_bank_list';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        //業務別
        $ddl->addColumn(MySQLColumnFactory::buildColumn('service', 'varchar', ['length' => 64, 'COMMENT' => '業務別']));
        //銀行代號/BIC
        $ddl->addColumn(MySQLColumnFactory::buildColumn('bic', 'varchar', ['length' => 16, 'COMMENT' => '銀行代號/BIC']));
        //金融機構名稱
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 64, 'COMMENT' => '金融機構名稱']));
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Index('bic'));
        $this->runSeed();
    }
    
    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/TwBankListInsert.php';
        require $filepath;
        $seed = new \TwBankListInsert($this->adapter);
        $this->seed = $seed;
    }
    
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
