<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Chopin\LaminasDb\Sql\Ddl\Constraint\MySQL\IndexConstraint;
use Laminas\Db\Sql\Ddl\Index\Index;

//ADD INDEX `parent` (`parent_id` ASC);

class Migrate_Create_elfinder_file_20190523145000 extends AbstractMigration
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
    protected $table = 'elfinder_file';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('parent_id', 'int', ['unsigned' => true, 'default' =>0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 256]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('content', 'longblob'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('size', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mitime', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('read', 'tinyint', ['length' => 1, 'default' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('write', 'tinyint', ['length' => 1, 'default' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locked', 'tinyint', ['length' => 1, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('hidden', 'tinyint', ['length' => 1, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('width', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('height', 'int', ['unsigned' => true, 'default' => 0]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\UniqueKey(['parent_id', 'name'], $this->table.'_parent_name'));
        $ddl->addConstraint(new Index('parent_id', $this->tailTable.'_idx_locale'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
