<?php

namespace Chopin\LaminasDb\RowGateway;

use Laminas\Db\RowGateway\RowGateway as LaminasRowGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\Sql\Predicate\PredicateTrait;
use Chopin\LaminasDb\DB;

class RowGateway extends LaminasRowGateway implements \Serializable
{
    use PredicateTrait;
    use RowTrait;

    protected $with = [];

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
     * @see \Laminas\Db\RowGateway\AbstractRowGateway::__get()
     */
    public function __get($name)
    {
        $this->with($name);
        if (isset($this->with[$name])) {
            return $this->with[$name];
        } else {
            if ($name == 'table') {
                return $this->table;
            }
            return parent::__get($name);
        }
    }

    /**
     *
     * @param string $table
     * @param bool $isExplodeManyToMany 拆解多對多關聯
     * @return \stdClass|\Laminas\Db\ResultSet\ResultSet
     */
    public function hasAnyOrThrough($table)
    {
        $this->singletonSql();
        $adapter = $this->sql->getAdapter();
        $schema = $adapter->getDriver()->getConnection()->getCurrentSchema();
        $tailTable = str_replace(AbstractTableGateway::$prefixTable, '', $table);
        $mapperClassname = file_get_contents('storage/database/'.$schema.'/'.$tailTable.'/tablegateway_mapper.dat');
        $reflection = new \ReflectionClass($mapperClassname);
        /**
         *
         * @var AbstractTableGateway $tableGateway
         */
        $tableGateway = $reflection->newInstance($adapter);
        $joinsScript = $tableGateway->buildInnerJoinScript();
        $idValue = intval($this->id);
        $bindKeyname = $this->table.'.id';
        $where = [
            ['equalTo', 'and', [$bindKeyname, $idValue]],
        ];
        if (array_search('deleted_at', $tableGateway->getColumns()) !== false) {
            $where[] = ['isNull', 'and', [$tableGateway->table.'.deleted_at']];
        }
        $order = [];
        if (array_search('sort', $tableGateway->getColumns()) !== false) {
            $order[] = $tableGateway->table.'.sort ASC';
        }
        if (array_search('id', $tableGateway->getColumns()) !== false) {
            $order[] = $tableGateway->table.'.id ASC';
        }

        $resultSet = DB::selectFactory([
            'from' => $tableGateway->table,
            'where' => $where,
            'join' => $joinsScript,
            'order' => $order,
        ]);
        $this->with[$tailTable] = $resultSet;

        return $this->with[$tailTable];
    }


    /**
     *
     * @param string $table
     * @param bool $isExplodeManyToMany
     * @return mixed|NULL|\stdClass|\Laminas\Db\ResultSet\ResultSet
     */
    public function with($table)
    {
        $sql = $this->singletonSql();
        $adapter = $sql->getAdapter();
        $tailTable = str_replace(AbstractTableGateway::$prefixTable, '', $table);
        if (isset($this->with[$tailTable])) {
            return $this->with[$tailTable];
        }
        $table = AbstractTableGateway::$prefixTable.$tailTable;

        /**
         *
         * @var \Laminas\Db\Adapter\Driver\Pdo\Connection $connection
         */
        $connection = $adapter->getDriver()->getConnection();
        $schema = $connection->getCurrentSchema();

        $mapperPath = 'storage/database/'.$schema. '/'.$tailTable.'/tablegateway_mapper.dat';

        if (is_file($mapperPath)) {
            $reflection = new \ReflectionClass(file_get_contents($mapperPath));
            $forigen_table = str_replace(AbstractTableGateway::$prefixTable, '', $this->table);
            /**
             *
             * @var AbstractTableGateway $tablegateway
             */
            $tablegateway = $reflection->newInstance($adapter);
            $columns = $tablegateway->getColumns();
            $table_id_verify = array_search('table_id', $columns) !== false;
            $forigen_verify = array_search($forigen_table.'_id', $columns) !== false;
            $parent_verify = array_search('parent_id', $columns) !== false;
            $deleted_at_verify = array_search('deleted_at', $columns) !== false;
            $sort_verify = array_search('sort', $columns) !== false;
            //多對一
            if ($table_id_verify || $forigen_verify || $parent_verify) {
                $selfname = $this->table;
                $selfTailname = str_replace(AbstractTableGateway::$prefixTable, '', $selfname);
                if ($table_id_verify) {
                    $where = [
                        ['like', 'and', ['table', '%'.$selfTailname]],
                        ['equalTo', 'and', ['table_id', $this->id]],
                    ];
                    if ($deleted_at_verify !== false) {
                        $where[] = ['isNull', 'and', ['deleted_at']];
                    }
                    $order = [];
                    if ($sort_verify !== false) {
                        $order[] = 'sort ASC';
                    }
                    if (false !== array_search('id', $columns)) {
                        $order[] = 'id ASC';
                    }
                    $resultSet = DB::selectFactory([
                        'from' => $tablegateway->table,
                        'where' => $where,
                        'order' => $order,
                    ]);
                    $this->with[$tailTable] = $resultSet;
                    return $this->with[$tailTable];
                }
                if ($forigen_verify) {
                    // 一對多

                    if (($forigen_table.'_id') != 'table_id') {
                        $r_table_primary = $tablegateway->primary;

                        if (false !== array_search('id', $r_table_primary)) {
                            $forigen_table_id = $forigen_table . '_id';
                            //$forigen_table_id => $this->id,
                            $where[] = ['equalTo', 'and', [$forigen_table_id, $this->id]];
                            if (array_search('deleted_at', $tablegateway->getColumns()) !== false) {
                                $where[] = ['isNull', 'and', ['deleted_at']];
                            }
                            $order = [];
                            if ($sort_verify !== false) {
                                $order[] = 'sort ASC';
                            }
                            $this->with[$tailTable] = DB::selectFactory([
                                'from' => $tablegateway,
                                'where' => $where,
                                'order' => $order,
                            ]);
                            return $this->with[$tailTable];
                        } else {
                            return $this->hasAnyOrThrough($table);
                        }
                    }
                }
                if ($parent_verify) {
                    $where = [
                        ['equalTo', 'and', ['parent_id', $this->id]],
                    ];
                    $_columns = $tablegateway->getColumns();
                    if (false !== array_search('deleted_at', $_columns)) {
                        if ($deleted_at_verify !== false) {
                            $where[] = ['isNull', 'and', ['deleted_at']];
                        }
                    }
                    $order = [];
                    if (false !== array_search('sort', $_columns)) {
                        if ($sort_verify !== false) {
                            $order[] = 'sort ASC';
                        }
                    }
                    if (false !== array_search('created_at', $_columns)) {
                        $order[] = 'created_at DESC';
                    }

                    $resultSet = DB::selectFactory([
                        'from' => $tablegateway->table,
                        'where' => $where,
                        'order' => $order,
                    ]);
                    $this->with['children'] = $resultSet;
                    return isset($this->with[$tailTable]) ? $this->with[$tailTable] : null;
                }
                return null;
            }

            $field = $tailTable.'_id';
            if ($this->offsetExists($field)) {
                //一對一
                $idValue = $this->{$field};
                $resultSet = DB::selectFactory([
                    'from' => $tablegateway->table,
                    'where' => [
                        ['equalTo', 'and', ['id', $idValue]],
                    ],
                ]);
                $resultSet->rewind();
                $this->with[$tailTable] = $resultSet;
                return $resultSet;
            } else {
                //一對多
                return $this->hasAnyOrThrough($table);
            }
        } else {
            $tailTable = str_replace(AbstractTableGateway::$prefixTable, '', $table);
            $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);
            $tables = $metadata->getTableNames();
            if (false !== array_search(AbstractTableGateway::$prefixTable.$tailTable, $tables)) {
                $tablegateway = AbstractTableGateway::newInstance($tailTable, $adapter);
                return $this->with($tablegateway->table);
            }
        }
        return null;
    }

    /**
     * @param string $withDataKey, 某些情況下可以用資料欄位的值做索引就會用到了，這邊指的是column name
     *
     * {@inheritDoc}
     * @see \Laminas\Db\RowGateway\AbstractRowGateway::toArray()
     */
    public function toArray($withDataKey=null)
    {
        $data = $this->data;

        $keys = array_keys($this->with);
        foreach ($keys as $name) {
            $this->With($name);
        }
        $with = [];
        foreach ($this->with as $name => $j) {
            if ($j) {
                $tmp = $j->toArray();
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
