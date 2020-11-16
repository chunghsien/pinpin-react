<?php

namespace Chopin\LaminasDb\Sql\Ddl\Column\MySQL;

trait ColumnPositionTrait
{

    /**
     * @return string
     */
    protected function getColumnPositionExpression()
    {
        if (isset($this->options['position'])) {
            return $this->options['position'];
        }
        return '';
    }

    /**
     *
     * @return array
     */
    public function getExpressionData()
    {
        $data = [];
        $data = parent::getExpressionData();
        if ($this->getColumnPositionExpression()) {
            $data[0][1][1] .= ' '.$this->getColumnPositionExpression();
        }

        return $data;
    }
}
