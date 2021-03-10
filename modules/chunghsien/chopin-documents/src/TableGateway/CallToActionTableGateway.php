<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class CallToActionTableGateway extends AbstractTableGateway
{
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'call_to_action';
    
    public function getFromDocuments($route)
    {
        $documentsTableGateway = new DocumentsTableGateway($this->adapter);
        $row = $documentsTableGateway->select(["route" => $route])->current();
        $selfRow = $this->select(["table" => "documents","table_id" => $row->id])->current();
        $selfRow->tags = json_decode($selfRow->tags, true);
        return $selfRow;
    }
}
