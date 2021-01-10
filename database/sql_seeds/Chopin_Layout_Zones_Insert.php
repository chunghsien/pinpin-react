<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Chopin_Layout_Zones_Insert extends AbstractSeeds
{
    protected $table = 'layout_zones';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->table);
        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'type' => 'footer',
            'name'=> '頁腳(1)- 中文(臺灣)',
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'type' => 'footer',
            'name'=> '頁腳(2)- 中文(臺灣)',
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
        $insert->values([
            'language_id' => 119,
            'locale_id' => 229,
            'type' => 'footer',
            'name'=> '頁腳(3)- 中文(臺灣)',
        ]);
        $sql->prepareStatementForSqlObject($insert)->execute();
    }
}
