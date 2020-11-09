<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert8 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $set = [
            'language_id' => 119,
            'locale_id' => 229,
            'parent_id' => 0,
            'key' => 'facebook_dev',
            'name' => 'Facebook 開發者',
        ];
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
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
                'language_id' => 119,
                'locale_id' => 229,
                'parent_id' => $parant_id,
                'key' => 'fb_colon_app_id',
                'name' => '應用程式編號',
                'input_type' => '{ "type": "text"}',
                //'deleted_at' => date("Y-m-d H:i:s"),
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'parent_id' => $parant_id,
                'key' => 'client_token',
                'name' => '用戶端權杖',
                'input_type' => '{ "type": "text"}',
                //'deleted_at' => date("Y-m-d H:i:s"),
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
