<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\LanguageHasLocale\TableGateway\LocaleTableGateway;
use Laminas\Db\Sql\Predicate\Operator;
use Chopin\LanguageHasLocale\TableGateway\LanguageTableGateway;
use Chopin\LanguageHasLocale\TableGateway\CurrenciesTableGateway;
use Laminas\Db\TableGateway\TableGateway;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

class Chopin_System_Settings_Insert2 extends AbstractSeeds
{

    protected $table = 'system_settings';

    public function run()
    {
        $systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
        $set = [
            'parent_id' => 0,
            'key' => 'localization',
            'name' => '本地化設定',
        ];
        if ($systemSettingsTableGateway->select($set)->count() > 0) {
            return;
        }

        $sql = new Sql($this->adapter);
        $insert = $sql->insert($this->table);
        $insert->values($set);
        $result = $sql->prepareStatementForSqlObject($insert)->execute();
        $parant_id = $result->getGeneratedValue();
        $pt = self::$prefixTable;

        $localeTableGateway = new TableGateway($pt . 'locale', $this->adapter);
        $localeRow = $localeTableGateway->select([
            'id' => '229'
        ])->current();
        $languageTableGateway = new TableGateway($pt . 'language', $this->adapter);
        $languageRow = $languageTableGateway->select([
            'id' => 119,
        ])->current();

        $currenciesTableGateway = new TableGateway($pt . 'currencies', $this->adapter);
        $currenciesRow = $currenciesTableGateway->select([
            'id' => 139
        ])->current();

        $datas = [
            [
                'parent_id' => $parant_id,
                'key' => 'country',
                'name' => '國家',
                'input_type' => json_encode([
                    'type' => 'select',
                    'required' => true,
                    [
                        'class' => LocaleTableGateway::class,
                        'method' => 'getOptions',
                        'params' => [
                            'id',
                            'name',
                            [
                                [
                                    'equalTo',
                                    'AND',
                                    [
                                        'is_use',
                                        1
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'value' => $localeRow->id,
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'administration_language',
                'name' => '語言',
                'input_type' => json_encode([
                    'type' => 'select',
                    'required' => true,
                    [
                        'class' => LanguageTableGateway::class,
                        'method' => 'getOptions',
                        'params' => [
                            'id',
                            'display_name',
                            [
                                [
                                    'equalto',
                                    'AND',
                                    [
                                        'is_use',
                                        1
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'value' => $languageRow->id,
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'administration_locale',
                'name' => '地區',
                'input_type' => json_encode([
                    'type' => 'select',
                    'required' => true,
                    [
                        'class' => LocaleTableGateway::class,
                        'method' => 'getOptions',
                        'params' => [
                            'id',
                            'name',
                            [
                                [
                                    'equalTo',
                                    'AND',
                                    [
                                        'is_use',
                                        1
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'value' => $localeRow->id,
            ],
            [
                'parent_id' => $parant_id,
                'key' => 'currency',
                'name' => '貨幣',
                'input_type' => json_encode([
                    'type' => 'select',
                    'required' => true,
                    [
                        'class' => CurrenciesTableGateway::class,
                        'method' => 'getOptions',
                        'params' => [
                            'id',
                            'name',
                            [
                                [
                                    'equalTo',
                                    'AND',
                                    [
                                        'is_use',
                                        1
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'value' => $currenciesRow->id,
            ],
        ];

        foreach ($datas as $data) {
            $insert = $sql->insert($this->table)->values($data);
            $sql->prepareStatementForSqlObject($insert)->execute();
        }
    }
}
