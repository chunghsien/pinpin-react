<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_assets_20190807040018 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    /**
     *
     * @var string
     */
    protected $table = 'assets';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', [ 'unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', [ 'unsigned' => true, 'default' => 0]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('table', 'varchar', ['nullable' => true, 'length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table_id', 'int'));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('name', 'varchar', ['nullable' => true, 'length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('path', 'varchar', ['length' => 256]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('mime', 'varchar', ['length' => 32]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_top', 'tinyint', ['length' => 1, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', [ 'default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));

        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Index(['table', 'table_id'], $this->tailTable . '_idx_table_table_id'));
        $ddl->addConstraint(new Index(['sort'], $this->tailTable . '_idx_sort'));
        $ddl->addConstraint(new Index(['is_top'], $this->tailTable.'_idx_is_top'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
