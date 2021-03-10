<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert3 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        $set = [
            'parent_id' => 0,
            'key' => 'google_service',
            'name' => 'Google 服務',
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
                'key' => 'ga_serail',
                'name' => 'Google Analytics 序號',
                'input_type' => json_encode([
                    'type' => 'text',
                    'required' => true,
                ]),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'gsv_serial',
                'name' => 'Google site verification 序號',
                'input_type' => json_encode([
                    'type' => 'text',
                    'required' => true,
                ]),
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
