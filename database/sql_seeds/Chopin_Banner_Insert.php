<?php
use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Laminas\Db\Sql\Sql;
use Chopin\Documents\TableGateway\BannerTableGateway;
use Laminas\Filter\Word\CamelCaseToUnderscore;

class Chopin_Banner_Insert extends AbstractSeeds
{

    public function run()
    {
        $tableGateway = new BannerTableGateway($this->adapter);
        $dir = __DIR__;
        $data = array_merge(
            json_decode(file_get_contents("{$dir}/hero-slider-one.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-two.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-three.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-four.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-five.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-six.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-seven.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-eight.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-nine.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-ten.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-eleven.json"), true),
            json_decode(file_get_contents("{$dir}/hero-slider-twelve.json"), true)
        );
        $filter = new CamelCaseToUnderscore();
        foreach ($data as $item) {
            $columns = array_keys($item);
            
            $set = [];
            foreach ($columns as $col) {
                $ch = strtolower($filter->filter($col));
                if($ch != 'id') {
                    $set[$ch] = $item[$col];
                }
            }
            $set['language_id'] = 119;
            $set['locale_id'] = 229;
            if(empty($set['type'])) {
                $set['type'] = 'carousel';
            }
            
            if($tableGateway->select($set)->count() === 0) {
                $tableGateway->insert($set);
            }
        }
        
    }
}
