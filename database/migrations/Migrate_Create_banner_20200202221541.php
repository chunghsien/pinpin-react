<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
//use Laminas\Db\Sql\Ddl\Constraint\UniqueKey;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_banner_20200202221541 extends AbstractMigration
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
    protected $table = 'banner';

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
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table_id', 'int', ['unsigned' => true, 'default' => 0]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('type', 'varchar', ['length' => 16]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('title', 'varchar', ['length' => 192, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('subtitle', 'varchar', ['length' => 192, 'nullable' => true]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('image', 'varchar', ['length' => 384, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('bg_image', 'varchar', ['nullable' => true, 'length' => 255]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('bg_color', 'varchar', ['nullable' => true, 'length' => 10]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('target', 'varchar', ['length' => 10, 'default' => 'self']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('url', 'varchar', ['length' => 384, 'nullable' => true]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_show', 'tinyint', ['unsigned' => true, 'length' => 1, 'default' => 1]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('sort', 'mediumint', ['unsigned' => true, 'default' => '16777215']));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Index(['language_id']));
        $ddl->addConstraint(new Index(['locale_id']));
        $this->runSeed();
    }

    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Chopin_Banner_Insert.php';
        require $filepath;
        $seed = new \Chopin_Banner_Insert($this->adapter);
        $this->seed = $seed;
    }
    
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
