<?php

namespace Chopin\LaminasDb\RowGateway;

use Laminas\Db\RowGateway\RowGateway as LaminasRowGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\Sql\Predicate\PredicateTrait;
use Chopin\LaminasDb\DB;
use Laminas\Db\Sql\Sql;

class RowGateway extends LaminasRowGateway implements \Serializable, \JsonSerializable
{
    use PredicateTrait;
    //use RowTrait;

    protected $with = [];


    public function __construct(...$args)
    {
        if (count($args) == 1) {
            $pt = AbstractTableGateway::$prefixTable;
            $table = $this->table;
            $this->table = "{$pt}{$table}";
            $adapter = $args[0];
            $this->sql = new Sql($adapter, $this->table);
            $this->initialize();
        } else {
            parent::__construct($args[0], $args[1], $args[2]);
        }
    }

    public function serialize()
    {
        $this->sql = null;
        return serialize($this);
    }

    public function unserialize($str)
    {
        /**
         *
         * @var RowGateway $rowGateway
         */
        $rowGateway = unserialize($str);
        $rowGateway->singletonSql();
        return $rowGateway;
    }

    /**
     *
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Laminas\Db\RowGateway\AbstractRowGateway::__get()
     */
    public function __get($name)
    {
        if (isset($this->with[$name])) {
            return $this->with[$name];
        } else {
            if ($name == 'tablename') {
                return $this->table;
            }

            if ($name == 'table' && isset($this->data['table'])) {
                return $this->data['table'];
            }
            return parent::__get($name);
        }
    }

    /**
     * 
     * @param string $name
     * @param \Iterator $resultSet
     */
    public function with($name, $resultSet)
    {
        $this->with[$name] = $resultSet;
    }
    
    /**
     * @return bool
     */
    public function rowExistsInDatabase()
    {
        if(empty($this->primaryKeyData)) {
            return false;
        }
        if($this->primaryKeyData) {
            $select = $this->sql->select()->where($this->primaryKeyData);
            $result = $this->sql->prepareStatementForSqlObject($select)->execute();
            return $result->count() >= 1;
        }
        return ($this->primaryKeyData !== null);
    }
    
    /**
     *
     * @param string $withDataKey,
     * {某些情況下可以用資料欄位的值做索引就會用到了，這邊指的是column name}
     * {@inheritdoc}
     * @see \Laminas\Db\RowGateway\AbstractRowGateway::toArray()
     */
    public function toArray($withDataKey = null)
    {
        $data = $this->data;

        $with = [];
        foreach ($this->with as $name => $j) {
            if ($j) {
                $isToArrayMethodExists = false;
                if($j instanceof \Iterator) {
                    $jClass = get_class($j);
                    $jClassMethods = get_class_methods($jClass);
                    //debug($jClassMethods);
                    if(false !== array_search('toArray', $jClassMethods)) {
                        $isToArrayMethodExists = true;
                    }
                }
                $tmp = is_array($j) ? $j : ($isToArrayMethodExists ? $j->toArray() : $j);
                if ($withDataKey && isset($this->data[$withDataKey])) {
                    $with[$name] = [];
                    foreach ($tmp as $tv) {
                        $keyValue = $tv[$withDataKey];
                        $with[$name][$keyValue] = $tv;
                    }
                } else {
                    $with[$name] = $tmp;
                }
            }
        }
        $this->data = array_merge($data, $with);
        return parent::toArray();
    }
}
