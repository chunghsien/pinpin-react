<?php

namespace Chopin\LaminasDb\Console\SqlExecute;

use Laminas\Db\Adapter\Adapter;

abstract class AbstractSeeds
{
    /**
     *
     * @var Adapter
     */
    protected $adapter;


    /**
     *
     * @var string
     */
    protected $table;

    protected $tailTable;

    public static $prefixTable;

    public function __construct(Adapter $adapter)
    {
        $config = config('db.adapters');
        $pt = $config[Adapter::class]['prefix'];
        $this->tailTable = $this->table;
        if ($pt) {
            $this->table = $pt.$this->tailTable;
        }
        self::$prefixTable = $pt;
        $this->adapter = $adapter;
    }

    public function __get($name)
    {
        $name = strtolower($name);
        switch ($name) {
            case 'tailTable':
                return $this->tailTable;
                break;
            case 'table':
                return $this->table;
                break;
        }

        return null;
    }

    abstract public function run();
}
