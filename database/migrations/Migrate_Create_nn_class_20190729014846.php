<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Laminas\Db\Sql\Ddl\Index\Index;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_nn_class_20190729014846 extends AbstractMigration
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
    protected $table = 'nn_class';

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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['length' => 128]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('image', 'varchar', ['length' => 384, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('viewed_count', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        //$ddl->addConstraint(new Constraint\UniqueKey('name', $this->tailTable.'_name_UNIQUE'));
        $ddl->addConstraint(new Index(['language_id'], $this->tailTable.'_idx_1'));
        $ddl->addConstraint(new Index(['language_id', 'locale_id'], $this->tailTable.'_idx_2'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
