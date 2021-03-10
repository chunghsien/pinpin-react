<?php
namespace Chopin\Store\RowGateway;

use Chopin\LaminasDb\RowGateway\RowGateway;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\Store\TableGateway\NpClassHasProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\LaminasDb\ResultSet\ResultSet;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Laminas\Db\Sql\Expression;

class ProductsRowGateway extends RowGateway
{

    protected $table = 'products';

    protected $primaryKeyColumn = [
        "id"
    ];
    
    public function withItemSumStock($id)
    {
        ProductsSpecTableGateway::$isRemoveRowGatewayFeature = true;
        $adapter = $this->sql->getAdapter();
        $productsSpecTableGateway = new ProductsSpecTableGateway($adapter);
        //;
        $select = $productsSpecTableGateway->getSql()->select();
        $where = $select->where;
        $where->isNull('deleted_at');
        $where->equalTo('products_id', $id);
        $select->columns(['sum_stock' => new Expression("SUM(`stock`)")]);
        $adapter->getDriver()->getConnection()->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $item = $productsSpecTableGateway->selectWith($select)->current();
        ProductsSpecTableGateway::$isRemoveRowGatewayFeature = false;
        $this->with["sum_stock"] = $item['sum_stock'];
    }
    
    public function withAssets()
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
    
    public function withNpClass($complex = false)
    {
        $npClassTableGateway = new NpClassTableGateway($this->sql->getAdapter());
        $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->sql->getAdapter());
        $select = $npClassHasProductsTableGateway->getSql()->select();
        $select->columns([]);
        $products_id = $this->data['id'];
        $select->join($npClassTableGateway->table, "{$npClassHasProductsTableGateway->table}.np_class_id={$npClassTableGateway->table}.id", [
            "id",
            "name"
        ]);
        $predicate = $select->where;
        $predicate->equalTo("{$npClassHasProductsTableGateway->table}.products_id", $products_id);
        $select->where($predicate);
        $resultSet = $npClassHasProductsTableGateway->selectWith($select);
        $data = [];
        foreach ($resultSet as $row) {
            if($complex) {
                $data[] = $row;
            }else{
                $data[] = $row['name'];
            }
            
        }
        $this->with["np_class"] = $data;
    }
    
    public function withSpec($id = null)
    {
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->sql->getAdapter());
        $select = $productsSpecTableGateway->getSql()->select();
        $select->columns(['name', 'extra_name', 'triple_name', 'stock']);
        $select->quantifier("DISTINCT");
        $where = $select->where;
        $where->isNull('deleted_at');
        if($id) {
            $where->equalTo('id', $id);
        }
        $dataSource = $productsSpecTableGateway->getSql()->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Laminas\Db\ResultSet\ResultSet();
        $resultSet->initialize($dataSource);
        if($id) {
            $this->with['spec'] = (array)$resultSet->current();
            return ;
        }
        $this->with['spec'] = $resultSet->toArray();
    }
    
    protected function getSpecGroupWithSpec($specGroupId)
    {
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->sql->getAdapter());
        $where = $productsSpecTableGateway->getSql()->select()->where;
        $where->isNull('deleted_at');
        $where->equalTo('products_spec_group_id', $specGroupId);
        $specsRow = $productsSpecTableGateway->select($where)->current();
        return $specsRow->toArray();
    }
    public function withSpecGroup($id = null)
    {
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->sql->getAdapter());
        $where = $productsSpecGroupTableGateway->getSql()->select()->where;
        $where->isNull('deleted_at');
        $where->equalTo('products_id', $this->data['id']);
        if($id) {
            $where->equalTo('id', $id);
            $result = $productsSpecGroupTableGateway->select($where)->current();
            $specGroup = $result->toArray();
            $specGroup['spec'] = $this->getSpecGroupWithSpec($id);
            $this->with['spec_group'] = $specGroup;
            return;
        }
        $resultSet = $productsSpecGroupTableGateway->select($where);
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->sql->getAdapter());
        $result = [];
        foreach ($resultSet as $row) {
            if(!$id) {
                $where = $productsSpecTableGateway->getSql()->select()->where;
                $where->isNull('deleted_at');
                $where->equalTo('products_spec_group_id', $row->id);
                $specsResultSet = $productsSpecTableGateway->select($where);
                $spec = [];
                foreach ($specsResultSet as $specRow) {
                    $key = $specRow->name;
                    $spec[$key] = $specRow->toArray();
                }
                $row->with('spec', $spec);
            }
            $result[] = $row->toArray();
        };
        $this->with['spec_group'] = $result;
    }
}