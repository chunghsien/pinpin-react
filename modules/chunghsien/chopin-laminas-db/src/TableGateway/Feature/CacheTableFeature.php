<?php

namespace Chopin\LaminasDb\TableGateway\Feature;

use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class CacheTableFeature extends EventFeature
{

    /*
    public function postSelect(StatementInterface $statement, ResultInterface $result, ResultSetInterface $resultSet)
    {
        parent::postSelect($statement, $result, $resultSet);
    }
    */

    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        parent::postInsert($statement, $result);
        $this->refresh();
    }
    
    public function postUpdate(StatementInterface $statement, ResultInterface $result)
    {
        parent::postUpdate($statement, $result);
        $this->refresh();
    }

    public function postDelete(StatementInterface $statement, ResultInterface $result)
    {
        parent::postDelete($statement, $result);
        $this->refresh();
    }
    
    private function refresh()
    {
        $tablename = $this->tableGateway->getTable();
        $tablename = str_replace(AbstractTableGateway::$prefixTable, '', $tablename);
        DbCacheMapperTableGateway::refreash($tablename);
    }
}
