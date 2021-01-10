<?php
namespace Chopin\Store\RowGateway;

use Chopin\LaminasDb\RowGateway\RowGateway;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\Store\TableGateway\NpClassHasProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\LaminasDb\ResultSet\ResultSet;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;

class ProductsRowGateway extends RowGateway
{

    protected $table = 'products';

    protected $primaryKeyColumn = [
        "id"
    ];
    
    public function withImage()
    {
        $adapter = $this->sql->getAdapter();
        $assetsTableGateway = new AssetsTableGateway($adapter);
        $select = $assetsTableGateway->getSql()->select();
        $select->columns(['id', 'path']);
        $predicate = $select->where;
        $predicate->like('table', "%products");
        $predicate->equalTo('table_id', $this->id);
        $select->where($predicate);
        $select->order('sort asc, id asc');
        $resultSet = $assetsTableGateway->selectWith($select);
        $withData = [];
        $thumbs = [];
        foreach ($resultSet as $key => $row)
        {
            $withData[] = $row->path;
            if($key < 2) {
                $thumbs[] = $row->path;
            }
        }
        $this->with['image'] = $withData;
        $this->with['thumbImage'] = $thumbs;
    }
    
    public function withCategory()
    {
        $npClassTableGateway = new NpClassTableGateway($this->sql->getAdapter());
        $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->sql->getAdapter());
        $select = $npClassHasProductsTableGateway->getSql()->select();
        $products_id = $this->data['id'];
        $select->join($npClassTableGateway->table, "{$npClassHasProductsTableGateway->table}.np_class_id={$npClassTableGateway->table}.id", [
            "name"
        ]);
        $predicate = $select->where;
        $predicate->equalTo("{$npClassHasProductsTableGateway->table}.products_id", $products_id);
        $select->where($predicate);
        $resultSet = $npClassHasProductsTableGateway->selectWith($select);
        $data = [];
        foreach ($resultSet as $row) {

            $data[] = $row['name'];
        }
        $this->with["category"] = $data;
    }

    public function withVariation()
    {
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->sql->getAdapter());
        $products_id = $this->data['id'];
        $productsSpecGroupSelect = $productsSpecGroupTableGateway->getSql()->select();
        $productsSpecGroupSelect->columns([
            "id",
            "color" => "name",
            "colorCode" => "extra_name",
            "image"
        ])->where([
            'products_id' => $products_id
        ]);
        $productsSpecGroupResultSetSource = $productsSpecGroupTableGateway->selectWith($productsSpecGroupSelect);
        $productsSpecGroupDataSource = [];
        foreach ($productsSpecGroupResultSetSource as $row) {
            $row->withSize();
            if($row->size) {
                $productsSpecGroupDataSource[] = $row;
            }
            
        }
        $resultSet = new ResultSet();
        $resultSet->initialize($productsSpecGroupDataSource);
        $this->with['variation'] = $resultSet;
    }
    public function withTag()
    {
        if(empty($this->with["category"])) {
            $this->withCategory();
        }
        $this->with["tag"] = $this->with["category"];
    }
}