<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Psr\Http\Message\ServerRequestInterface;

class ProductsSpecGroupAttrsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'products_spec_group_attrs';

    /**
     *
     * @param ServerRequestInterface $request
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getAll(ServerRequestInterface $request)
    {
        $language_id = $request->getAttribute('language_id');
        $locale_id = $request->getAttribute('locale_id');
        $select = $this->sql->select();
        $where = $select->where;
        $where->isNull('deleted_at');
        $where->equalTo('language_id', $language_id);
        $where->equalTo('locale_id', $locale_id);
        $select->columns([
            'id',
            'name',
            'extra_name',
            'image'
        ]);
        return $this->selectWith($select);
    }
}
