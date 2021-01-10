<?php

namespace Chopin\LaminasDb\ResultSet;

use Laminas\Db\ResultSet\ResultSet as LaminasResultSet;

class ResultSet extends LaminasResultSet implements \JsonSerializable
{

    /**
     *
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}