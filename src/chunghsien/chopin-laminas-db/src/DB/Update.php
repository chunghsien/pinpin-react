<?php

namespace Chopin\LaminasDb\DB;

use Laminas\Db\Sql\Update as LaminasDbUpdate;
use Laminas\Db\Sql\TableIdentifier;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;

class Update extends LaminasDbUpdate
{
    use Traits\CommonTrait;
    use Traits\SecurityTrait;
    use Traits\CacheTrait;
    use Traits\Profiling;

    /**
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor
     *
     * @param  string|TableIdentifier|AbstractTableGateway $table
     */
    public function __construct($table)
    {
        if ($table instanceof AbstractTableGateway) {
            $this->tablegateway = $table;
            $table = $this->tablegateway->table;
        }
        $this->initCrypt();
        parent::__construct($table);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Db\Sql\Update::set()
     */
    public function set(array $values, $flag = self::VALUES_SET)
    {
        //對需要的欄位作加密
        $values = $this->securty($values);
        $values = $this->filterEmptyValues($values);
        return parent::set($values, $flag);
    }
    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function excute()
    {
        $set = $this->getRawState('set');
        $setKeys = array_keys($set);
        $filePattern = '/(file|photo|path|avater|image|banner)$/i';
        $fileKeys = [];
        foreach ($setKeys as $key) {
            if (preg_match($filePattern, $key)) {
                $fileKeys[] = $key;
            }
        }

        //後面再改用session處裡，如果有交易應該要在整個交易跑完在處裡。
        if ($fileKeys) {
            $where = $this->getRawState('where');
            $row = $this->tablegateway->select($where)->current();
            foreach ($fileKeys as $fk) {
                if (isset($row->{$fk}) && is_file($row->{$fk})) {
                    unlink($row->{$fk});
                }
            }
        }
        $this->getTableGateway();
        $sql = $this->tablegateway->getSql();
        $result = $sql->prepareStatementForSqlObject($this)->execute();
        $this->runDbProfiling($sql->buildSqlString($this));
        if ($this->getEnvCacheUse()) {
            DbCacheMapperTableGateway::refreash($this->table);
        }
        return $result;
    }
}
