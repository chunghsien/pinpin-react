<?php

namespace Chopin\LaminasDb\DB\Traits;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\Adapter\Adapter;
use Chopin\LaminasDb\DB\Insert;
use Chopin\LaminasDb\DB\Update;

trait CommonTrait
{

    /**
     *
     * @var \Chopin\LaminasDb\TableGateway\AbstractTableGateway
     */
    protected $tablegateway;

    protected function filterEmptyValues($values)
    {
        if ($this instanceof Insert || $this instanceof Update) {
            if (is_array($values) && isset($values[0])) {
                foreach ($values as &$item) {
                    foreach ($item as $key => $value) {
                        if (is_null($value) || (is_string($value) && strlen($value) == 0)) {
                            unset($item[$key]);
                        }
                    }
                }
            } else {
                foreach ($values as $key => $value) {
                    if (is_null($value) || (is_string($value) && strlen($value) == 0)) {
                        unset($values[$key]);
                    }
                }
            }
        }
        return $values;
    }
    /**
     *
     * @return \Chopin\LaminasDb\TableGateway\AbstractTableGateway
     */
    public function getTableGateway()
    {
        if ($this->tablegateway instanceof AbstractTableGateway == false && $this->table) {
            if (is_array($this->table)) {
                //fixed 加密部分
                $keys = array_keys($this->table);
                $key = $keys[0];
                $table = str_replace('_decrypt', '', $key);
                $table = str_replace(AbstractTableGateway::$prefixTable, '', $table);
            } else {
                $table = str_replace(AbstractTableGateway::$prefixTable, '', $this->table);
            }
            $adapter = GlobalAdapterFeature::getStaticAdapter();
            $schema = $adapter->getCurrentSchema();

            $this->tablegateway = AbstractTableGateway::newInstance($table, $adapter);

            // if($this instanceof Select) {
            if ($this->is_profiling === false) {
                $config = config('db.adapters.' . Adapter::class);
                if (isset($config['profiling'])) {
                    $profiling = $config['profiling'];
                    if ($profiling === true) {
                        $this->is_profiling = true;
                        $adapter = $this->tablegateway->getSql()->getAdapter();
                        $adapter->query('set profiling=1;', Adapter::QUERY_MODE_EXECUTE);
                    }
                }
            }
            //}
        }
        if ($this->tablegateway instanceof AbstractTableGateway) {
            return $this->tablegateway;
        } else {
            return null;
        }
    }
}
