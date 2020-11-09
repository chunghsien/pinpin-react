<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_member_has_member_roles_20201023023853 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'create';
    
    protected $priority = 3;
    
    /**
     * 
     * @var string
     */
    protected $table = 'member_has_member_roles';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('member_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('member_roles_id', 'int', ['unsigned' => true]));
        
        //首次建立為now()
        $ddl->addColumn(MySQLColumnFactory::buildColumn('activation', 'date'));
        //預設資料基本會員(member_role.name == 'base_member')設成2099-12-31
        $ddl->addColumn(MySQLColumnFactory::buildColumn('expiration', 'date'));
        
        $ddl->addConstraint(new Constraint\ForeignKey('fk_' . $this->tailTable . '_member1', 'member_id', self::$prefixTable.'users', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey('fk_' . $this->tailTable . 'member_roles1', 'member_roles_id', self::$prefixTable.'member_roles', 'id'));
        $ddl->addConstraint(new Constraint\PrimaryKey(['member_id', 'member_roles_id']));
        
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
