<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_contact_20190724055851 extends AbstractMigration
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
    protected $table = 'contact';

    protected $priority = 3;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('full_name', 'varbinary', ['length' => 96, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('phone', 'varbinary', ['length' => 32, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('email', 'varbinary', ['length' => 512]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('subject', 'varchar', ['length' => 128, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('commet', 'varchar', ['length' => 1024]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('reply', 'varchar', ['length' => 1024, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_reply', 'tinyint', ['unsigned' => true, 'default' => 0]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));

        $ddl->addConstraint(new Index(['language_id']));
        $ddl->addConstraint(new Index(['language_id', 'locale_id']));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
