<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Chopin\LaminasDb\RowGateway\RowGateway;

class Chopin_System_Settings_Insert4 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);
        $select = $sql->select($this->table)->where([
            'key' => 'google_service'
        ]);
        $current = $sql->prepareStatementForSqlObject($select)->execute()->current();
        $parant_id = $current['id'];
        $datas = [
            [
                'parent_id' => $parant_id,
                'key' => 'google_recaptcha_site_key',
                'name' => 'Google reCAPTCHA 網站金鑰',
                'input_type' => json_encode([
                    'type' => 'text',
                    'required' => true,
                ]),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'google_recaptcha_secret_key',
                'name' => 'Google reCAPTCHA 密鑰',
                'input_type' => json_encode([
                    'type' => 'text',
                    'required' => true,
                ]),
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'google_service_account_serect',
                'name' => 'Google Service Account 密鑰',
            ],
        ];

        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        foreach ($datas as $data) {
            if($systemSettingsTableGateway->select($data)->count() == 0) {
                $insert = $sql->insert($this->table)->values($data);
                $sql->prepareStatementForSqlObject($insert)->execute();
            }
        }
    }
}
