<?php

namespace Chopin\Newsletter\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB\Traits\SecurityTrait;

class ContactTableGateway extends AbstractTableGateway
{

    use SecurityTrait;

    public static $isRemoveRowGatewayFeature = false;

    public function __construct($adapter)
    {
        parent::__construct($adapter);
        $this->initCrypt();
    }

    /**
     *
     * @inheritdoc
     */
    protected $table = 'contact';

    /**
     *
     * {@inheritdoc}
     * @see \Laminas\Db\TableGateway\AbstractTableGateway::insert()
     */
    public function insert($set)
    {
        $set = $this->securty($set);
        return parent::insert($set);
    }

    public function update($set, $predicate = null, array $joins = null)
    {
        $set = $this->securty($set);
        if (isset($set['reply']) && strlen(trim($set['reply']))) {
            if (empty($set['is_reply']) || (isset($set['is_reply']) && $set['is_reply'] == 0)) {
                $set['is_reply'] = 1;
            }
        }
        return parent::update($set, $predicate, $joins);
        //return DB::table($this)->update($set, $predicate);
    }
}
