<?php

use Chopin\LaminasDb\Console\SqlExecute\AbstractSeeds;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Chopin\Documents\TableGateway\CallToActionTableGateway;

class Chopin_Call_To_Action extends AbstractSeeds
{

    public function run()
    {
        $tableGateway = new CallToActionTableGateway($this->adapter);
        $documentsTableGateway = new DocumentsTableGateway($this->adapter);
        $row = $documentsTableGateway->select(['route' => '/zh-TW'])->current();
        $set = [
            "image"=> "/assets/images/cta/cabinet.jpg",
            "tags"=> json_encode(["summer", "shelf", "sale"]),
            "title"=> "Up To 40% Off Final Sale Items. <br> Caught in the moment!",
            "url"=> "/shop/left-sidebar",
            "table" => "documents",
            "table_id" => $row->id,
        ];
        $tableGateway->insert($set);
   }
}
