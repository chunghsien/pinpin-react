<?php

namespace Chopin\Store\RowGateway;

use Chopin\LaminasDb\RowGateway\RowGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;

class ProductsSpecGroupRowGateway extends RowGateway
{
    protected $table = 'products_spec_group';
    
    protected $primaryKeyColumn = [
        "id"
    ];
    
    public function withSize()
    {
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->sql->getAdapter());
        $productsSpecSelect = $productsSpecTableGateway->getSql()->select();
        $productsSpecSelect->columns(["id", "name", "stock"]);
        $products_spec_gorup_id = $this->data['id'];
        $productsSpecSelect->where(['products_spec_group_id' => $products_spec_gorup_id]);
        $resultSet = $productsSpecTableGateway->selectWith($productsSpecSelect);
        $this->with['size'] = $resultSet;
    }
}