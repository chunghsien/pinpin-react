<?php

namespace Chopin\LaminasDb\DB;

use Laminas\Db\Sql\Insert as LaminasDbInsert;
use Chopin\LaminasDb\DB\Traits\SecurityTrait;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\TableIdentifier;
use Chopin\LaminasDb\DB\Traits\CommonTrait;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;
use Chopin\LaminasDb\DB\Traits\CacheTrait;

class Insert extends LaminasDbInsert
{
    use SecurityTrait;
    use CommonTrait;
    use CacheTrait;
    use Traits\Profiling;

    /**
     *
     * @var string
     */
    protected $table;

    /**
     *
     * @var array
     */
    protected $values;

    protected $multipleFormat = 'INSERT INTO %s (%s) VALUES %s ;';


    /**
     * Constructor
     *
     * @param  null|string|TableIdentifier|AbstractTableGateway $table
     */
    public function __construct($table)
    {
        if ($table instanceof AbstractTableGateway) {
            $this->tablegateway = $table;
            $table = $this->tablegateway->getTable();
        }
        $this->initCrypt();
        parent::__construct($table);
    }

    protected function noPrimaryProcess($values)
    {
        if (isset($values[0])) {
            $container = [];
            foreach ($values as $value) {
                $keys = array_keys($value);
                if (count($container) == 0) {
                    foreach ($keys as $key) {
                        $container[$key] = [];
                    }
                }
                foreach ($keys as $key) {
                    $container[$key][] = $value[$key];
                }
            }
            $deleted_value = 0;
            $deleted_key = '';
            foreach ($container as $k => &$c) {
                $c = array_unique($c);
                $c = array_values($c);
                if (count($c) == 1) {
                    $deleted_value = intval($c[0]);
                    $deleted_key = $k;
                    break;
                }
            }
            if ($deleted_value && $deleted_key) {
                if ($this->tablegateway->select([$deleted_key => $deleted_value])->count() > 0) {
                    $this->tablegateway->delete([$deleted_key => $deleted_value]);
                }
            }
        } else {
            if ($this->tablegateway->select($values)->count() == 1) {
                $this->tablegateway->delete($values);
            }
        }
    }

    public function values($values, $flag = self::VALUES_SET)
    {
        $this->getTableGateway();

        if (false === array_search('id', $this->tablegateway->getColumns())) {
            $this->noPrimaryProcess($values);
        }

        $columns = $this->tablegateway->getColumns();
        if (is_array($values) && isset($values[0])) {
            foreach ($values as &$value) {
                if (is_array($value)) {
                    $value = $this->securty($value);
                    if (empty($value['created_at']) && false !== array_search('created_at', $columns)) {
                        $value['created_at'] = date("Y-m-d H:i:s");
                    }
                }
            }
            $this->values = $values;
            return parent::values($values[0], $flag);
        } else {
            if (is_array($values)) {
                $values = $this->securty($values);
                if (empty($values['created_at']) && false !== array_search('created_at', $columns)) {
                    $values['created_at'] = date("Y-m-d H:i:s");
                }
            }
        }
        $values = $this->filterEmptyValues($values);
        parent::values($values, $flag);
        return $this;
    }

    protected function creatQuenMark($value)
    {
        $mark = str_repeat('?,', count($value));
        $mark = preg_replace('/\,$/', '', $mark);
        $mark = '('.$mark.')';
        return $mark;
    }

    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function excute()
    {
        $this->getTableGateway();
        //logger()->info($this->tablegateway->getSql()->buildSqlString($this));
        if (is_array($this->values) && isset($this->values[0])) {
            /**
             *
             * @var \Laminas\Db\Adapter\Adapter $adapter
             */
            $adapter = $this->getTableGateway()->getAdapter();
            $tablename = $adapter->platform->quoteIdentifier($this->table);
            $columns = array_keys($this->values[0]);
            foreach ($columns as &$column) {
                $column = $adapter->platform->quoteIdentifier($column);
            }
            $columns = implode(',', $columns);
            $insertValues = [];
            $marks = [];
            foreach ($this->values as $value) {
                $marks[] = $this->creatQuenMark($value);
                $insertValues = array_merge($insertValues, array_values($value));
            }
            $sqlString = sprintf($this->multipleFormat, $tablename, $columns, implode(', ', $marks));
            $result = $adapter->query($sqlString)->execute($insertValues);
            $this->runDbProfiling($sqlString);
            if ($this->getEnvCacheUse()) {
                DbCacheMapperTableGateway::refreash($this->table);
            }

            return $result;
        } else {
            $sql = $this->getTableGateway()->getSql();
            $result = $sql->prepareStatementForSqlObject($this)->execute();
            $this->runDbProfiling($sql->buildSqlString($this));
            if ($this->getEnvCacheUse()) {
                DbCacheMapperTableGateway::refreash($this->table);
            }
            return $result;
        }
    }
}
