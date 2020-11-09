<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use Laminas\Db\Sql\Ddl\Constraint\UniqueKey;
use Laminas\Db\Sql\Ddl\Index\Index;

class Migrate_Create_facebook_tags_20191013100732 extends AbstractMigration
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
    protected $table = 'facebook_tags';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', ['unsigned' => true, 'auto_increment' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table', 'varchar', ['nullable' => true, 'length' => 64]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('table_id', 'int', ['unsigned' => true, ]));

        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_url', 'varchar', ['length' => 192, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_title', 'varchar', ['length' => 64, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_description', 'varchar', ['length' => 255, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_image', 'varchar', ['length' => 192, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('fb_colon_app_id', 'varchar', ['length' => 64, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_type', 'varchar', ['length' => 255, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_locale', 'varchar', ['length' => 96, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_video', 'varchar', ['length' => 192, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_video_colon_secure_url', 'varchar', ['length' => 192, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_video_colon_type', 'varchar', ['length' => 48, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_video_colon_width', 'varchar', ['length' => 32, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_video_colon_height', 'varchar', ['length' => 32, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_image_colon_secure_url', 'varchar', ['length' => 192, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_image_colon_type', 'varchar', ['length' => 48, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_image_colon_width', 'varchar', ['length' => 32, 'default' => '']));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('og_colon_image_colon_height', 'varchar', ['length' => 32, 'default' => '']));


        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new UniqueKey(['table', 'table_id']));
        $ddl->addConstraint(new Index(['table']));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
