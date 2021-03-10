<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Laminas\Db\TableGateway\TableGateway;

class Migrate_Alter_system_settings_20191203082541 extends AbstractMigration
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
    
    public function down()
    {
        //
    }
    
    public function up()
    {
        // 一開始只會打開繁體中文
        $languageTableGateway = new TableGateway(self::$prefixTable . 'language', $this->adapter);
        $localeTableGateway = new TableGateway(self::$prefixTable . 'locale', $this->adapter);
        
        
        $languageResult = $languageTableGateway->select([
            'is_use' => 1
        ])->current();
        $localeResult = $localeTableGateway->select([
            'is_use' => 1
        ])->current();

        $systemSettingsTableGateway = new TableGateway(self::$prefixTable . 'system_settings', $this->adapter);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => 0,
            'key' => 'general_seo',
            'name' => '基礎 SEO',
        ]);
        $lastID = $systemSettingsTableGateway->lastInsertValue;

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'title',
            'name' => '網站標題',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'meta_keyword',
            'name' => '網站關鍵字',
        ]);

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'textarea',
                'required' => false
            ]),
            'key' => 'meta_description',
            'name' => '網站描述',
        ]);

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => 0,
            'key' => 'site_info',
            'name' => '網站資料',
        ]);
        
        $lastID = $systemSettingsTableGateway->lastInsertValue;

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'name',
            'name' => '網站名稱',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'owner',
            'name' => '擁有者',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'email',
            'name' => '網站信箱',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'email_service_from_name',
            'name' => '信箱服務人員名稱',
        ]);

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'address',
            'name' => '住址',
        ]);

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'telphone',
            'name' => '聯絡電話',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'mobile',
            'name' => '手機',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'text',
                'required' => false
            ]),
            'key' => 'fax',
            'name' => '傳真',
        ]);
        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => $lastID,
            'input_type' => json_encode([
                'type' => 'textarea',
                'required' => false
            ]),
            'key' => 'operation',
            'name' => '營業時間',
        ]);
        
    }
}
