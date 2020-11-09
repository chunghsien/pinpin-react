<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert extends AbstractSeeds
{
    protected $table = 'system_settings';

    public function run()
    {
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        $set = [
            'parent_id' => 0,
            'key' => 'mail-service',
            'name' => '郵件主機設定',
        ];
        if($systemSettingsTableGateway->select($set)->count() > 0) {
            return ;
        }
        
        
        $sql = new Sql($this->adapter);
        $insert = $sql->insert($this->table);
        $insert->values($set);

        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $parant_id = $result->getGeneratedValue();

        $datas = [
            [
                'parent_id' => $parant_id,
                'key' => 'mail_method',
                'name' => '寄件方式',
                'value' => 'php sendmail',
                'input_type' => '{ "type": "select", "value": { "php_sendmail": "php_sendmail", "mail_server": "mail_server"}, "required":false }',
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'from',
                'name' => '回覆(寄件)信箱',
                'input_type' => '{ "type": "text"  , "required":true}',
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'host',
                'name' => '主機位置(名稱)',
                'input_type' => '{ "type": "text"  , "required":true}',
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'username',
                'name' => '主機帳號',
                'input_type' => '{ "type": "text"  , "required":true}',
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'password',
                'name' => '密碼',
                'input_type' => '{ "type": "password"  , "required":true}',
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'ssl',
                'name' => '連線加密方式',
                'value' => 0,
                'input_type' => '{ "type": "select", "value": { "0": "無", "ssl": "ssl", "tls": "tls" }, "required":false }',
            ],

            [
                'parent_id' => $parant_id,
                'key' => 'port',
                'name' => '連接埠',
                'input_type' => '{ "type": "number"  , "required":true}',
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
