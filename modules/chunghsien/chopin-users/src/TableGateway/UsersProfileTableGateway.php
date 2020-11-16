<?php

namespace Chopin\Users\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class UsersProfileTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'users_profile';

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\LaminasDb\TableGateway\AbstractTableGateway::insert()
     */
    public function insert($values)
    {
        $keys = array_keys($values);
        if (is_int($keys[0])) {
            foreach ($values as $value) {
                $update_verify = $this->updateVerify($value);
                if ($update_verify === false) {
                    break;
                }
            }
        } else {
            $update_verify = $this->updateVerify($values);
        }
        if ($update_verify === false) {
            return parent::insert($values);
        } else {
            return $update_verify;
        }
    }

    /**
     *
     * @param array $oneSet
     * @return \Laminas\Db\Adapter\Driver\ResultInterface|boolean
     */
    protected function updateVerify($oneSet)
    {
        // debug($oneSet, ['isContinue' => true]);
        if (empty($oneSet['id'])) {
            if (isset($oneSet['users_id']) && isset($oneSet['key'])) {
                $where = [
                    'users_id' => $oneSet['users_id'],
                    'key' => $oneSet['key'],
                ];
                $resultSet = $this->select($where);

                if ($resultSet->count() == 1) {
                    $row = $resultSet->current();
                    return $this->update($oneSet, [
                        'id' => $row->id
                    ]);
                }
            }
        }
        return false;
    }
}
