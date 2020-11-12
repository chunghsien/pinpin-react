<?php

namespace Chopin\LaminasDb\TableGateway\Feature;

use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Delete;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\TableIdentifier;

class PrefixTableFeature extends EventFeature
{

    /**
     *
     * @var AbstractTableGateway
     */
    protected $tableGateway;

    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\Feature\EventFeature::preSelect()
     */
    public function preSelect(Select $select)
    {
        $rawStaties = $select->getRawState();
        $newSelect = new Select();
        foreach ($rawStaties as $key => $raw) {
            if ( ! ($key == 'where' || $key == 'having' || $key == 'joins')) {
                $method = $key;
                if ($key == 'table') {
                    $method = 'from';
                    if ($raw instanceof TableIdentifier) {
                        $tablename = $raw->getTable();
                        $raw = new TableIdentifier($this->tableGateway->prefixTable.$tablename);
                    } else {
                        $raw = $this->tableGateway->prefixTable.$raw;
                    }
                }
                if ($raw) {
                    call_user_func_array([$newSelect, $method], [$raw]);
                }
            } else {
                if ($raw->count()) {
                    switch ($key) {
                        case 'where':
                            $newSelect->where($raw);
                            break;
                        case 'having':
                            $newSelect->having($raw);
                            break;
                        case 'joins':
                            $joins = $raw->joins->getJoins();
                            foreach ($joins as $join) {
                                call_user_func_array([$newSelect, 'join'], $join);
                            }
                            break;
                    }
                }
            }
        }

        parent::preSelect($newSelect);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\Feature\EventFeature::preInsert()
     */
    public function preInsert(Insert $insert)
    {
        $table = $insert->getRawState('table');
        $prefix = $this->tableGateway->prefixTable;
        $insert->into($prefix.$table);
        parent::preInsert($insert);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\Feature\EventFeature::preUpdate()
     */
    public function preUpdate(Update $update)
    {
        parent::preUpdate($update);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Db\TableGateway\Feature\EventFeature::preDelete()
     */
    public function preDelete(Delete $delete)
    {
        parent::preDelete($delete);
    }
}
