<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Laminas\Db\ResultSet\ResultSet;

class Countries_Flag_Insert extends AbstractSeeds
{
    protected $table = 'countries_flag';

    public function run()
    {
        $sql = new Sql($this->adapter);
        $countriesSelect = $sql->select(self::$prefixTable.'locale');
        $countriesDatasource = $sql->prepareStatementForSqlObject($countriesSelect)->execute();
        $countriesResultSet = new ResultSet();
        $countriesResultSet->initialize($countriesDatasource);
        $insertValues = [];
        foreach ($countriesResultSet as $country) {
            if (is_dir('./public')) {
                $svgPath = 'public/packages/country-flags/svg/'.$country['code'].'.svg';
                $png100Path = 'public/packages/country-flags/png100px/'.$country['code'].'.png';
                $png250Path = 'public/packages/country-flags/png250px/'.$country['code'].'.png';
                $png1000Path = 'public/packages/country-flags/png1000px/'.$country['code'].'.png';
            } else {
                $svgPath = 'assets/packages/country-flags/svg/'.$country['code'].'.svg';
                $png100Path = 'assets/packages/country-flags/png100px/'.$country['code'].'.png';
                $png250Path = 'assets/packages/country-flags/png250px/'.$country['code'].'.png';
                $png1000Path = 'assets/packages/country-flags/png1000px/'.$country['code'].'.png';
            }

            if (is_file($svgPath) && is_file($png100Path) && is_file($png250Path) && is_file($png1000Path)) {
                $insertValues[] = [
                    'locale_id' => $country['id'],
                    'svg_path' => preg_replace('/^public/', '', $svgPath),
                    'png100_path' => preg_replace('/^public/', '', $png100Path),
                    'png250_path' => preg_replace('/^public/', '', $png250Path),
                    'png1000_path' => preg_replace('/^public/', '', $png1000Path),
                ];
            }
        }

        foreach ($insertValues as $values) {
            $_insert = $sql->insert($this->table)->values($values);
            $sql->prepareStatementForSqlObject($_insert)->execute();
        }
    }
}
