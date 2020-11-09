<?php

namespace Chopin\LaminasDb\DB;

use Laminas\Db\Sql\Delete as LaminasDbDelete;
use Laminas\Db\Sql\TableIdentifier;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;
use Laminas\Db\RowGateway\RowGateway;

class Delete extends LaminasDbDelete
{
    use Traits\CommonTrait;
    use Traits\CacheTrait;
    use Traits\Profiling;

    protected $isSoftDelete = true;

    /**
     * Constructor
     *
     * @param  string|TableIdentifier|AbstractTableGateway $table
     */
    public function __construct($table = null, $isSoftDelete = true)
    {
        if ($table instanceof AbstractTableGateway) {
            $this->tablegateway = $table;
            $table = $this->tablegateway->table;
        }
        $this->isSoftDelete = $isSoftDelete;
        parent::__construct($table);
    }

    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function excute()
    {
        $tablegateway = $this->getTableGateway();
        $columns = $tablegateway->getColumns();
        $predicate = $this->getRawState('where');
        $sql = $this->tablegateway->getSql();

        $resultSet = $this->tablegateway->select($predicate);
        if ($resultSet->count() == 1) {
            $row = $resultSet->current();
            $back = [];
            if ($row instanceof RowGateway) {
                $back = $row->toArray();
            } else {
                $back = (array)$row;
            }

            //debug($back);
        }

        if ((false !==  array_search('deleted_at', $columns)) && $this->isSoftDelete) {
            $values = ['deleted_at' => date("Y-m-d H:i:s")];
            $update = $sql->update()->set($values)->where($predicate);
            $result = $sql->prepareStatementForSqlObject($update)->execute();
        }
        if (empty($result)) {
            $delete = $sql->delete()->where($predicate);
            $result = $sql->prepareStatementForSqlObject($delete)->execute();
        }
        if ($this->getEnvCacheUse()) {
            DbCacheMapperTableGateway::refreash($this->table);
        }
        $this->runDbProfiling();
        if (isset($back)) {
            //debug($back, ['save' => true]);
            $allowRemoveFileColumns = ['path', 'file', 'photo', 'image', 'avater', 'banner', 'main_photo', 'sub_photo', 'third_photo'];
            $keys = array_keys($back);
            $intersect = array_intersect($allowRemoveFileColumns, $keys);
            if ($intersect) {
                foreach ($intersect as $c) {
                    @unlink($back[$c]);
                }
            }
        }
        return $result;
    }
}
