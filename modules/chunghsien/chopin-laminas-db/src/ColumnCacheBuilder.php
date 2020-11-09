<?php

namespace Chopin\LaminasDb;

use Laminas\Db\Adapter\Adapter;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

abstract class ColumnCacheBuilder
{
    public static function createColumns(Adapter $adapter, $table)
    {

        /**
         *
         * @var \Laminas\Db\Metadata\Source\AbstractSource $metadata
         */
        $metadata = \Laminas\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);

        $dbname = $adapter->getCurrentSchema();

        //AbstractTableGateway::$prefixTable
        $tail_table = preg_replace('/^'.AbstractTableGateway::$prefixTable.'/', '', $table);
        $baseFolder = dirname(dirname(dirname(dirname(__DIR__)))).'/storage/database/'.$dbname.'/'.$tail_table;
        if ( ! is_dir($baseFolder)) {
            mkdir($baseFolder, 0755, true);
        }
        /**
         *
         * @var \Laminas\Db\Metadata\Object\ColumnObject[] $columns
         */
        $columns = $metadata->getColumns($table);

        $columnsTmp = [];
        foreach ($columns as $column) {
            $columnName = $column->getName();
            $columnsTmp[$columnName] = $column;
        }

        file_put_contents($baseFolder.'/columns.dat', serialize($columnsTmp));

        /**
         *
         * @var \Laminas\Db\Metadata\Object\ConstraintObject[] $constraints
         */
        $constraints = $metadata->getConstraints($table);
        $constraintsTmp = [];
        foreach ($constraints as $constraint) {
            $type = $constraint->getType();
            if (empty($constraintsTmp[$type])) {
                $constraintsTmp[$type] = [];
            }
            $constraintsTmp[$type][] = $constraint;
        }
        file_put_contents($baseFolder.'/constraints.dat', serialize($constraintsTmp));
    }
}
