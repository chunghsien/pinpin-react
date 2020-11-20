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
            'name'=> '首頁(繁體中文)',
            'route' => '/site/zh-TW',
            'allowed_methods' => json_encode(["GET"]),
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
