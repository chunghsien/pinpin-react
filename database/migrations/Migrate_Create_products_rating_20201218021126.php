<?php
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_products_rating_20201218021126 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'create';

    protected $priority = 2;

    /**
     *
     * @var string
     */
    protected $table = 'products_rating';

    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('id', 'int', [ 'unsigned' => true, 'auto_increment' => true ]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('products_id', 'int', [ 'unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('rating', 'tinyint', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('comment', 'varchar', ['length' => 512]));
        
        
        $ddl->addColumn(MySQLColumnFactory::buildColumn('deleted_at', 'datetime', ['nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('created_at', 'datetime', ['default' => new Expression('CURRENT_TIMESTAMP')]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('updated_at', 'timestamp', ['default' => new Expression('CURRENT_TIMESTAMP'), 'on_update' => true]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey('id', $this->tailTable . '_id_PRIMARY'));
        $ddl->addConstraint(new Constraint\ForeignKey( 'fk1_'.$this->tailTable, 'products_id', self::$prefixTable.'products', 'id'));
        
    }

    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
