<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert7 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $set = [
            'parent_id' => 0,
            'key' => 'newwebpay',
            'name' => '藍新金流參數',
        ];
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        if($systemSettingsTableGateway->select($set)->count() > 0) {
            return ;
        }

        $sql = new Sql($this->adapter);
        $insert = $sql->insert($this->table);
        $insert->values(array_merge($set, ['deleted_at' => date("Y-m-d H:i:s")]));

        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $parant_id = $result->getGeneratedValue();

        $datas = [
            [
                'parent_id' => $parant_id,
                'key' => 'merchant_id',
                'name' => '商店代碼',
                'input_type' => '{ "type": "text"  , "required":true, "disabled": true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'hash_key',
                'name' => 'API HashKey',
                'input_type' => '{ "type": "text"  , "required":true, "disabled": true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'hash_iv',
                'name' => 'API HashIV',
                'input_type' => '{ "type": "text"  , "required":true, "disabled": true}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'return_url',
                'name' => '支付返回網址',
                'input_type' => '{ "type": "text"  , "required":false, "disabled": true, "prompt": "若為空值，交易完成後，消費者將停留在藍新金流付款或取號完成頁面；只接受80與443 Port。"}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'notify_url',
                'name' => '支付通知網址',
                'input_type' => '{ "type": "text"  , "required":false, "disabled": true, "prompt": "以幕後方式回傳給商店相關支付結果資料；只接受80與443 Port。"}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'client_back_url',
                'name' => '支付取消返回網址',
                'input_type' => '{ "type": "text"  , "required":false, "disabled": true, "prompt": "當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面。"}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'customer_url',
                'name' => '商店取號網址',
                'input_type' => '{ "type": "text"  , "required":false, "disabled": true, "prompt": "此參數若為空值，則會顯示取號結果在藍新金流頁面。"}',
                'deleted_at' => date("Y-m-d H:i:s"),
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
