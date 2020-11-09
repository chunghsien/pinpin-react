<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_language_has_locale_20190725044917 extends AbstractMigration
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
    protected $table = 'language_has_locale';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('language_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('locale_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('code', 'varchar', ['length' => 20]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('display_name', 'varchar', ['length' => 255]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_use', 'tinyint', ['default' => 0]));

        $fkPrefix = 'fk_'.$this->tailTable;
        $ddl->addConstraint(new Constraint\PrimaryKey(['language_id', 'locale_id'], $this->tailTable.'_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkPrefix.'_language', 'language_id', self::$prefixTable.'language', 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkPrefix.'_locale', 'locale_id', self::$prefixTable.'locale', 'id'));
        $ddl->addConstraint(new Constraint\UniqueKey('code'));
        $ddl->addConstraint(new Constraint\UniqueKey('display_name'));

        $this->runSeed();
    }

    public function runSeed()
    {
        $filepath = dirname(__DIR__).'/sql_seeds/Language_Has_Locale_Insert.php';
        require $filepath;
        $seed = new \Language_Has_Locale_Insert($this->adapter);
        $this->seed = $seed;
    }


    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
