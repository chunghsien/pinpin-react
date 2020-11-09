<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_seo_20190807070733 extends AbstractMigration
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
    protected $table = 'seo';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', [
            'unsigned' => true,
            'auto_increment' => true,
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table', 'varchar', ['length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table_id', 'int'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('title', 'varchar', ['nullable' => true, 'length' => 255]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('keyword', 'varchar', ['nullable' => true, 'length' => 255]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('description', 'varchar', ['nullable' => true, 'length' => 512]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn(
            'created_at',
            'datetime',
            ['default' => new Expression('CURRENT_TIMESTAMP')]
        ));
        $ddl->addColumn(
            MySQLColumnFactory::buildColumn(
            'updated_at',
            'timestamp',
            ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]
        )
        );

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->table . '_id_PRIMARY'));
        $ddl->addConstraint(new Index('table_id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
