<?php

namespace Chopin\Ecpay\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class LogisticsUseTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'logistics_use';

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::insert()
     */
    public function insert($values)
    {
        $keys = array_keys($values);
        if (is_int($keys[0])) {
            foreach ($values as $key => $value) {
                $value['manufacturer'] = 'ecpay';
                $values[$key] = $value;
            }
        } else {
            $values['manufacturer'] = 'ecpay';
        }
        return parent::insert($values);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::update()
     */
    public function update($set, $where = null, array $joins = null)
    {
        $set['manufacturer'] = 'ecpay';
        return parent::update($set, $where, $joins);
    }
}
