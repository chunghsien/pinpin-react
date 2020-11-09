<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert5 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $set = [
            'parent_id' => 0,
            'key' => 'unsplash_api',
            'name' => 'Unsplash 服務',
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
                'parent_id' => $parant_id,
                'key' => 'unsplash_access_key',
                'name' => 'Unsplash 應用程式ID',
                'input_type' => json_encode([
                    'type' => 'text',
                    'required' => true,
                ]),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'unsplash_secret_key',
                'name' => 'Unsplash 序號',
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
