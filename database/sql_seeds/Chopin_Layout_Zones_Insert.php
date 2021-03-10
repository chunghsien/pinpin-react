<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Chopin\Documents\TableGateway\LayoutZonesTableGateway;

class Chopin_Layout_Zones_Insert extends AbstractSeeds
{

    protected $table = 'layout_zones';

    public function run()
    {
        $tableGateway = new LayoutZonesTableGateway($this->adapter);
        $sets = [
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 'header_nav',
                'name' => '頁首導覽',
            ],
            [
                'language_id' => 119,
                'locale_id' => 229,
                'type' => 'footer_nav',
                'name' => '頁尾導覽',
            ],
        ];
        foreach ($sets as $set)
        {
            if($tableGateway->select($set)->count() == 0)
            {
                $tableGateway->insert($set);
            }
        }
    }
}
