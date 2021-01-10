<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Chopin\Store\TableGateway\NpClassTableGateway;

class Chopin_NpClass_Insert extends AbstractSeeds
{

    public function run()
    {
        $dir = __DIR__;
        $categories = json_decode(file_get_contents("{$dir}/category-one.json"), true);
        $npClassTableGateway = new NpClassTableGateway($this->adapter);
        foreach ($categories as $set) {
            $set['language_id'] = 119;
            $set['locale_id'] = 229;
            if($npClassTableGateway->select($set)->count() == 0) {
                $npClassTableGateway->insert($set);
            }
        }
        
    }
}
