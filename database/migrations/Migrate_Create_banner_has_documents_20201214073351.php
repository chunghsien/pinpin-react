<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_banner_has_documents_20201214073351 extends AbstractMigration
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
    protected $table = 'banner_has_documents';

    protected $priority = 2;

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('banner_id', 'int', [
            'unsigned' => true
        ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('documents_id', 'int', [
            'unsigned' => true
        ]));

        $ddl->addConstraint(new Constraint\PrimaryKey([
            'banner_id',
            'documents_id'
        ], $this->tailTable . '_PRIMARY'));
        $fkprefix = 'fk_' . $this->tailTable;
        $prefixTable = self::$prefixTable;
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix . '_banner', 'banner_id', "{$prefixTable}banner", 'id'));
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix . '_documents', 'documents_id', "{$prefixTable}documents", 'id'));
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
