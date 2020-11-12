<?php

namespace Chopin\LaminasDb\Console\Migrations;

use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Ddl;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\AbstractSql;

abstract class AbstractMigration
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    /**
     *
     * @var Sql
     */
    protected $sql;

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var string
     */
    protected $table;

    protected $tailTable;

    /**
     *
     * @var AbstractSql
     */
    protected $ddl;

    /**
     * 執行的優先順序越前面的越優先執行(表之間有關聯的時候要設定)，MAX = 3
     *
     * @var integer
     */
    protected $priority = 1;

    // const MAX_PRIORITY = 3;

    /**
     *
     * @var AbstractSql
     */
    protected $roolbackDdl = null;

    protected $seed = null;

    public static $prefixTable = '';

    public function __construct(Adapter $adapter)
    {
        $config = config('db.adapters');
        $pt = $config[Adapter::class]['prefix'];

        $this->tailTable = $this->table;
        if (isset($pt) && $pt) {
            $this->table = $pt. $this->table;
        }
        self::$prefixTable = $pt;

        $this->adapter = $adapter;
        $this->sql = new Sql($adapter);
        switch ($this->type) {
            case 'create':
                $this->ddl = new Ddl\CreateTable($this->table);
                break;
            case 'drop':
                $this->ddl = new Ddl\DropTable($this->table);
                break;
            case 'alter':
                $this->ddl = new Ddl\AlterTable($this->table);
                break;
        }
    }

    abstract public function up();

    abstract public function down();

    public function __get($name)
    {
        $name = strtolower($name);
        switch ($name) {
            case 'tailtable':
                return $this->tailTable;
                break;
            case 'table':
                return $this->table;
                break;
            case 'seed':
                return $this->seed;
                break;
            case 'type':
                return $this->type;
            case 'ddl':
                return $this->ddl;
            case 'roolbackddl':
                return $this->roolbackDdl;
            case 'priority':
                return $this->priority;

        }

        return null;
    }
}
