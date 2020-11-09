<?php

namespace Chopin\LaminasDb\Adapter\Driver;

use Laminas\Db\Adapter\Driver\ResultInterface;

final class EmptyResult implements ResultInterface
{
    public function __construct()
    {
    }

    public function buffer()
    {
        return;
    }

    public function isBuffered()
    {
        return false;
    }

    public function isQueryResult()
    {
        return false;
    }

    public function getAffectedRows()
    {
        return 0;
    }

    public function getGeneratedValue()
    {
        return null;
    }

    public function getResource()
    {
        return null;
    }

    public function getFieldCount()
    {
        return 0;
    }

    public function count()
    {
        return 0;
    }

    public function current()
    {
        return null;
    }

    public function next()
    {
        return;
    }

    public function key()
    {
        return 0;
    }

    public function valid()
    {
        return false;
    }

    public function rewind()
    {
        return ;
    }
}
