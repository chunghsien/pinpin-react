<?php

namespace Chopin\Validator\Db;

use Laminas\Validator\Db\RecordExists;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class CrcDataExists extends RecordExists
{
    public function __construct($options)
    {
        $adapter = GlobalAdapterFeature::getStaticAdapter();
        $options['adapter'] = $adapter;
        $table = $options['table'];
        $table = preg_replace('/^'.AbstractTableGateway::$prefixTable.'/', '', $table);
        $table = (AbstractTableGateway::$prefixTable.$table);
        $options['table'] = $table;
        parent::__construct($options);
    }
    /**
     *
     * {@inheritDoc}
     * @see \Laminas\Validator\Db\RecordExists::isValid()
     */
    public function isValid($value)
    {
        $value = crc32($value);
        return parent::isValid($value);
    }
}
