<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Chopin_Documents_Insert extends AbstractSeeds
{

    protected $table = 'documents';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);
        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'type' => 1,
            'name' => '首頁 - 中文(臺灣)',
            'route' => '/zh-TW',
            'allowed_methods' => json_encode([
                "GET"
            ]),
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();

        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'type' => 1,
            'name' => '聯絡我們 - 中文(臺灣)',
            'route' => '/zh-TW/contact',
            'allowed_methods' => json_encode([
                "GET"
            ]),
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
