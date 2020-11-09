<?php
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Migrate_Alter_system_settings_20201030064736 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'alter';

    /**
     *
     * @var string
     */
    protected $table = 'system_settings';

    protected $priority = 3;

    public function up()
    {
        $tablegateway = new SystemSettingsTableGateway($this->adapter);
        $parentSet = [
            'parent_id' => 0,
            'key' => 'system',
            'name' => '系統設定'
        ];
        // $parent_id = 0;
        if ($tablegateway->select($parentSet)->count() == 0) {
            $tablegateway->insert($parentSet);
        }
        $parentRow = $tablegateway->select($parentSet)->current();
        $sets = [
            [
                'parent_id' => $parentRow->id,
                'input_type' => json_encode([
                    'type' => 'file',
                    'required' => false,
                ]),
                'key' => 'comp_logo',
                'name' => '公司標誌(logo)'
            ],
            [
                'parent_id' => $parentRow->id,
                'input_type' => json_encode([
                    'type' => 'file',
                    'required' => false,
                ]),
                'key' => 'watermark',
                'name' => '浮水印'
            ],
        ];
        foreach ($sets as $set) {
            if ($tablegateway->select($set)->count() === 0) {
                $tablegateway->insert($set);
            }
        }
    }
    
    public function down()
    {
        //
    }
}
