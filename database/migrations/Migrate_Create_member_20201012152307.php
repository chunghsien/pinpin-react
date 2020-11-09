<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Constraint\UniqueKey;

class Migrate_Create_member_20201012152307 extends AbstractMigration
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
    protected $table = 'member';
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('parent_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('depth', 'tinyint', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('account', 'varchar', ['length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('full_name', 'varbinary', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('cellphone', 'varbinary', ['length' => 96]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('email', 'varbinary', ['length' => 384]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('country', 'varchar', ['length' => 128, 'default' => 'Republic of China']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('state', 'varchar', ['length' => 128, 'default' => 'Taiwan']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('zip', 'varchar', ['length' => 20, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('county', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('district', 'varchar', ['length' => 196, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('address', 'varbinary', ['length' => 384, 'nullable' => true]));
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('password', 'varchar', ['length' => 255]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('temporay_password', 'varchar', ['length' => 255, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('salt', 'varchar', ['length' => 8]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_fb_account', 'tinyint', ['unsigned' => true, 'default' => 0, 'length' => 1]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new UniqueKey(['account']));
        $ddl->addConstraint(new UniqueKey(['cellphone']));
        $ddl->addConstraint(new UniqueKey(['email']));
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
