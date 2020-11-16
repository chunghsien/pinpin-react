<?php

namespace Chopin\LaminasDb\RowGateway;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\RowGateway\Feature\FeatureSet;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\Exception\RuntimeException;

trait RowTrait
{
    public function __construct($primaryKeyColumn, $table, $adapterOrSql = null)
    {
        // setup primary key
        $this->primaryKeyColumn = empty($primaryKeyColumn) ? null : (array) $primaryKeyColumn;

        // set table
        $this->table = $table;

        if ($adapterOrSql instanceof Sql) {
            $this->sql = $adapterOrSql;
        } elseif ($adapterOrSql instanceof AdapterInterface) {
            $this->sql = new Sql($adapterOrSql, $this->table);
        }
        if ($this->sql instanceof Sql) {
            $this->initialize();
        }
    }

    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }

        if ( ! $this->featureSet instanceof FeatureSet) {
            $this->featureSet = new FeatureSet;
        }

        $this->featureSet->setRowGateway($this);
        $this->featureSet->apply('preInitialize', []);
        if ( ! is_string($this->table) && ! $this->table instanceof TableIdentifier) {
            throw new RuntimeException('This row object does not have a valid table set.');
        }

        if ($this->primaryKeyColumn === null) {
            throw new RuntimeException('This row object does not have a primary key column set.');
        } elseif (is_string($this->primaryKeyColumn)) {
            $this->primaryKeyColumn = (array) $this->primaryKeyColumn;
        }
        $this->featureSet->apply('postInitialize', []);
        $this->isInitialized = true;
    }

    /**
     *
     * @return \Laminas\Db\Sql\Sql
     */
    protected function singletonSql()
    {
        if ( ! $this->sql) {
            $adapter = GlobalAdapterFeature::getStaticAdapter();
            $this->sql = new Sql($adapter, $this->table);
        }
        return $this->sql;
    }
}
