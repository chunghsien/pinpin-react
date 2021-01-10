<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Diactoros\ServerRequest;
use Chopin\LaminasDb\ResultSet\ResultSet;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Db\Sql\Select;
use Chopin\Store\RowGateway\ProductsRowGateway;
use Laminas\Paginator\Adapter\DbTableGateway;


class ProductsTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'products';
    
    public function buildBaseSelect(ServerRequest $request, $type='popular', $category = '', array $paginatorOptions=[])
    {
        $language_id = $request->getAttribute('language_id', 119);
        $locale_id = $request->getAttribute('locale_id', 119);
        $select = $this->getSql()->select();
        $select->quantifier("distinct");
        $select->columns([
            "id",
            "saleCount" => "sale_count",
            "name" => "model",
            "slug" => "alias",
            "shortDescription" => "introduction",
            "fullDescription" => "detail",
            "price",
            "real_price",
        ]);
        
        $predicate = $select->where;
        $productsIdentifyTableGateway = new ProductsIdentifyTableGateway($this->adapter);
        
        $select->join(
            $productsIdentifyTableGateway->table,
            "{$this->table}.id={$productsIdentifyTableGateway->table}.products_id",
            ["sku"],
            "left"
        );
        $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->adapter);
        $select->join(
            $npClassHasProductsTableGateway->table,
            "{$this->table}.id={$npClassHasProductsTableGateway->table}.products_id",
            []
            );
        $npClassTableGateway = new NpClassTableGateway($this->adapter);
        $select->join(
            $npClassTableGateway->table,
            "{$npClassHasProductsTableGateway->table}.np_class_id={$npClassTableGateway->table}.id",
            []
        );
        $productsDiscountTableGateway = new ProductsDiscountTableGateway($this->adapter);
        $select->join(
            $productsDiscountTableGateway->table,
            "{$productsDiscountTableGateway->table}.products_id={$this->table}.id",
            ["discount"],
            "left"
        );
        
        if(!$paginatorOptions) {
            $paginatorOptions['itemCountPerPage'] = 10;
            $paginatorOptions['currentPageNumber'] = 1;
        }
        $query = $request->getQueryParams();
        if(isset($query['page'])) {
            $paginatorOptions['currentPageNumber'] = $query['page'];
        }
        if(isset($query['count-per-page'])) {
            $paginatorOptions['itemCountPerPage'] = $query['count-per-page'];
        }
        
        switch ($type)
        {
            case 'poupular':
                $select->order('sale_count DESC');
                break;
            case 'new':
                $predicate->equalTo('is_new', 1);
                $select->order('id DESC');
                break;
            case 'sale':
                $predicate->lessThanOrEqualTo("{$productsDiscountTableGateway->table}.end_date", date("Y-m-d H:i:s"));
                break;
            default:
                $select->order('id DESC');
                break;
        }
        if($category) {
            $predicate->expression("lower({$npClassTableGateway->table}.name) = ?", [strtolower($category)]);
            //$predicate->equalTo("{$npClassTableGateway->table}.name", $category);
        }
        $predicate->equalTo("{$this->table}.language_id", $language_id);
        $predicate->equalTo("{$this->table}.locale_id", $locale_id);
        $predicate->isNull("{$this->table}.deleted_at");
        
        $select->where($predicate);
        $adapter = new DbSelect($select, $this->adapter);
        $paginator = new Paginator($adapter);
        foreach ($paginatorOptions as $option => $value)
        {
            $option = ucfirst($option);
            $method = "set{$option}";
            $paginator->{$method}($value);
        }
        $items = $paginator->getCurrentItems();
        $pages = $paginator->getPages();
        foreach ($items as $key => $item)
        {
            $row = new ProductsRowGateway($this->adapter);
            $row->exchangeArray((array)$item);
            $row->withCategory();
            $row->withTag();
            $row->withVariation();
            $row->withImage();
            $items[$key] = $row;
        }
        return [
            "items" => (array)$items,
            "pages" => $pages,
        ];
    }
    
    /**
     * 
     * @param ServerRequest $request
     * @param number $limit
     * @param string $category
     * @param string $return
     * @return array|\stdClass
     */
    public function getPoupular($request, $limit = 10, $category = "",  string $return="items")
    {
        $result = $this->buildBaseSelect($request, "poupular", $category, ["itemCountPerPage" => $limit]);
        if($return == 'items') {
            return $result['items'];
        }
        if($return == 'pages') {
            return $result['pages'];
        }
        return $result;
    }
    
    /**
     *
     * @param ServerRequest $request
     * @param integer $limit
     * @param string $category
     */
    public function getNew($request, $limit = 10, $category = "")
    {
        $result = $this->buildBaseSelect($request, "new", $category, ["itemCountPerPage" => $limit]);
        return $result['items'];
    }

    /**
     *
     * @param ServerRequest $request
     * @param integer $limit
     * @param string $category
     */
    public function getSale($request, $limit = 10, $category = "")
    {
        $result = $this->buildBaseSelect($request, "sale", $category, ["itemCountPerPage" => $limit]);
        return $result['items'];
    }
    
}
