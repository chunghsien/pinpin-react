<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;

class Currencies_Insert extends AbstractSeeds
{
    protected $table = 'currencies';

    public function run()
    {
        $sql = new Sql($this->adapter);

        $currenciesCode = json_decode(file_get_contents('vendor/sokil/php-isocodes/databases/iso_4217.json'), true)['4217'];
        $insertValues = [];
        foreach ($currenciesCode as $c) {
            $code = $c['alpha_3'];
            $name = $c['name'];
            $is_use = 0;
            if (strtolower($code) == 'twd') {
                $is_use = 1;
            }
            $insertValues[] = [
                'code' => $code,
                'name' => $name,
                'is_use' => $is_use,
            ];
        }

        foreach ($insertValues as $values) {
            $_insert = $sql->insert($this->table)->values($values);
            $sql->prepareStatementForSqlObject($_insert)->execute();
        }
    }
}
