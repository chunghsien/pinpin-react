<?php

namespace Chopin\Users\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Math\Rand;
use Laminas\Validator\EmailAddress;
use Chopin\LaminasDb\DB;

class UsersTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'users';

    /**
     *
     * @param \Iterator $items
     * @return \Iterator
     */
    public function mergeUsersProfileToItems(\Iterator $items)
    {
        $tablegateway = $this->newInstance('users_profile', $this->adapter);
        foreach ($items as &$row) {
            $resultSet = $tablegateway->select([
                'users_id' => $row['id']
            ]);
            foreach ($resultSet as $subRow) {
                $name = $subRow->key;
                $value = $subRow->value ? $subRow->value : $subRow->aes_value;
                $row[$name] = $value;
            }
        }
        return $items;
    }

    /**
     *
     * @param array|\ArrayAccess $row
     * @param array $all
     * @return array|\ArrayAccess
     */
    public function buildUsersProfileAllToRow($row, $all)
    {
        $result = isset($row['users_profile']) ? $row['users_profile'] : [];
        $keys = [];
        foreach ($result as $subRow) {
            $keys[] = $subRow['key'];
        }
        $diff = [];
        if ($keys) {
            $diff = array_diff($all, $keys);
        } else {
            $diff = $all;
        }
        foreach ($diff as $key) {
            $result[] = [
                'users_id' => $row['id'],
                'key' => $key,
            ];
        }
        $row['users_profile'] = $result;
        return $row;
    }

    public function insert($values)
    {
        // 會員email儲存成帳號的過渡方法
        if (isset($values['account'])) {
            $validator = new EmailAddress();
            if ($validator->isValid($values['account'])) {
                $_account = $values['account'] . uniqid() . microtime() . time();
                $values['account'] = crc32($_account);
            }
        }
        return parent::insert($values);
    }

    public function roles_idToPredicate($field, $value)
    {
        return new Like($field, "%$value%");
    }

    public function emailToPredicate($field, $value)
    {
        $usersProfileTableGateway = new UsersProfileTableGateway($this->adapter);
        $field = $usersProfileTableGateway->table . '.aes_value';
        $predicate = new Predicate();
        $predicate->equalTo($usersProfileTableGateway->table . '.key', 'email');

        $encryptionOptions = config('encryption');
        $aesKey = $encryptionOptions['aes_key'];
        $like = "'%$value%'";
        $predicate->expression("CAST(AES_DECRYPT({$usersProfileTableGateway->table}.`aes_value`, '$aesKey') AS CHAR) LIKE $like", []);

        return $predicate;
    }

    public function cellphoneToPredicate($field, $value)
    {
        $usersProfileTableGateway = new UsersProfileTableGateway($this->adapter);
        $field = $usersProfileTableGateway->table . '.aes_value';
        $predicate = new Predicate();
        $predicate->equalTo($usersProfileTableGateway->table . '.key', 'cellphone');

        $encryptionOptions = config('encryption');
        $aesKey = $encryptionOptions['aes_key'];
        $like = "'%$value%'";
        $predicate->expression("CAST(AES_DECRYPT({$usersProfileTableGateway->table}.`aes_value`, '$aesKey') AS CHAR) LIKE $like", []);

        return $predicate;
    }

    public function full_nameToPredicate($field, $value)
    {
        $usersProfileTableGateway = new UsersProfileTableGateway($this->adapter);
        $field = $usersProfileTableGateway->table . '.value';
        $predicate = new Predicate();
        $predicate->equalTo($usersProfileTableGateway->table . '.key', 'full_name');
        $predicate->like($usersProfileTableGateway->table . '.value', '%' . $value . '%');

        return $predicate;
    }

    /**
     * 回傳users的個人資訊
     *
     * @param array $row
     * @param boolean $flat
     * @return array
     */
    public function rowInjectProfile($row, $flat = true)
    {
        $users_id = $row['id'];
        $adapter = $this->adapter;
        $UsersProfileTableGateway = new UsersProfileTableGateway($adapter);
        $resultSet = DB::selectFactory([
            'from' => $UsersProfileTableGateway->table,
            'where' => [
                'equalTo',
                'and',
                [
                    'users_id',
                    $users_id
                ],
            ],
        ]);
        if ($flat) {
            foreach ($resultSet as $profile) {
                $key = $profile->key;
                if ($profile->value) {
                    $row[$key] = $profile->value;
                }
                if ($profile->aes_value) {
                    $row[$key] = $profile->aes_value;
                }
            }
        } else {
            $row['with'] = [];
            $row['with']['users_profiles'] = $resultSet;
        }
        return $row;
    }

    /**
     * 取得會員的新帳號
     *
     * @return string
     */
    public function getRandMemberAccount()
    {
        $account = 'member_'.Rand::getString(8, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        if ($this->select(['account' => $account])->count() === 0) {
            return $account;
        }
        return $this->getRandMemberAccount();
    }
}
