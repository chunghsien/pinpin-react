<?php

namespace Chopin\Documents\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class LayoutZonesTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'layout_zones';
    
    public function getChildren($parent_id)
    {
        $select = $this->sql->select();
        $where = $select->where;
        $where->isNull('deleted_at');
        $where->equalTo('parent_id', $parent_id);
        $select->where($where);
        $select->order(['sort asc', 'id asc']);
        return $this->selectWith($select);
    }
}
