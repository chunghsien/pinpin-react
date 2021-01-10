<?php

use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;

class Migrate_Create_layout_zones_has_documents_20201215080917 extends AbstractMigration
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
    protected $table = 'layout_zones_has_documents';
    
    protected $priority = 2;
    
    public function up()
    {
        /**
         *
         * @var \Laminas\Db\Sql\Ddl\CreateTable $ddl
         */
        $ddl = $this->ddl;
        $ddl->addColumn(MySQLColumnFactory::buildColumn('layout_zones_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('documents_id', 'int', ['unsigned' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('alias', 'varchar', ['length' => 64, 'nullable' => true]));
        $ddl->addColumn(MySQLColumnFactory::buildColumn('is_show_childs', 'tinyint', ['length' => 1, 'default' => 0]));
        
        $ddl->addConstraint(new Constraint\PrimaryKey(['layout_zones_id', 'documents_id'], $this->tailTable.'_PRIMARY'));
        $fkprefix = 'fk_'.$this->tailTable;
        $prefixTable = self::$prefixTable;
        
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_layout_zones', 'layout_zones_id', "{$prefixTable}layout_zones", 'id'));
        
        $ddl->addConstraint(new Constraint\ForeignKey($fkprefix.'_documents', 'documents_id', "{$prefixTable}documents", 'id'));
        
    }
    
    public function down()
    {
        $this->roolbackDdl = new \Laminas\Db\Sql\Ddl\DropTable($this->table);
    }
}
