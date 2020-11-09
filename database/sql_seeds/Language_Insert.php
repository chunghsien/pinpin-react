<?php
/**
 * *資料來源：composer require umpirsky/locale-list
 */

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;

class Language_Insert extends AbstractSeeds
{
    protected $table = 'language';

    public function run()
    {
        $initialSql = file_get_contents('database/sql_seeds/language.sql.stub');
        $initialSql = str_replace('`?`', sprintf('`%s`', $this->table), $initialSql);
        $this->adapter->createStatement($initialSql)->execute();
    }
}
