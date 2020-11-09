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
            'name' => sprintf('基礎SEO (%s)', $languageResult->display_name),
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
            'name' => sprintf('網站標題 (%s)', $languageResult->display_name),
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
            'name' => sprintf('網站關鍵字 (%s)', $languageResult->display_name),
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
            'name' => sprintf('網站描述 (%s)', $languageResult->display_name),
        ]);

        $systemSettingsTableGateway->insert([
            'language_id' => $languageResult->id,
            'locale_id' => $localeResult->id,
            'parent_id' => 0,
            'key' => 'site_info',
            'name' => sprintf('網站資料 (%s)', $languageResult->display_name),
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
            'name' => sprintf('網站名稱 (%s)', $languageResult->display_name),
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
            'name' => sprintf('擁有者 (%s)', $languageResult->display_name),
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
            'name' => sprintf('網站信箱 (%s)', $languageResult->display_name),
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
            'name' => sprintf('信箱服務人員名稱 (%s)', $languageResult->display_name),
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
            'name' => sprintf('住址 (%s)', $languageResult->display_name),
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
            'name' => sprintf('聯絡電話 (%s)', $languageResult->display_name),
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
            'name' => sprintf('傳真 (%s)', $languageResult->display_name),
        ]);
    }
}
