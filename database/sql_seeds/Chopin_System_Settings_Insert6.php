<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert6 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        $set = [
            'parent_id' => 0,
            'key' => 'ecpay',
            'name' => '綠界金流參數',
        ];

        if ($systemSettingsTableGateway->select($set)->count() > 0) {
            return;
        }

        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);
        $insert->values($set);

        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $parant_id = $result->getGeneratedValue();

        $datas = [
            [
                'parent_id' => $parant_id,
                'key' => 'merchant_id',
                'name' => '商店代碼',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'aio_hash_key',
                'name' => '金流 HashKey',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'aio_hash_iv',
                'name' => '金流 HashIV',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'invoice_hash_key',
                'name' => '電子發票 HashKey',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'invoice_hash_iv',
                'name' => '電子發票 HashIV',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],

            [
                'parent_id' => $parant_id,
                'key' => 'logistic_hash_key',
                'name' => '物流 HashKey',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'logistic_hash_iv',
                'name' => '物流 HashIV',
                'input_type' => '{ "type": "text"  , "required":true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
