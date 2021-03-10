<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Diactoros\ServerRequest;
use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\DbSelect;
use Chopin\Store\RowGateway\ProductsRowGateway;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Predicate\PredicateSet;

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
        $productsIdentityTableGateway = new ProductsIdentityTableGateway($this->adapter);
        
        $select->join(
            $productsIdentityTableGateway->table,
            "{$this->table}.id={$productsIdentityTableGateway->table}.products_id",
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
    
    public function getPaginator(ServerRequest $request, $countPerPage=20)
    {
        $language_id = $request->getAttribute('language_id');
        $locale_id = $request->getAttribute('locale_id');
        $method_or_id = $request->getAttribute('method_or_id');
        $this->adapter->getDriver()->getConnection()->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        
        $query = $request->getQueryParams();
        
        $select = $this->sql->select();
        $select->quantifier("distinct");
        $where = $select->where;
        $where->isNull("{$this->table}.deleted_at");
        $where->equalTo("{$this->table}.language_id", $language_id);
        $where->equalTo("{$this->table}.locale_id", $locale_id);
        
        //產品關鍵字搜尋
        if($method_or_id == 'search')
        {
            if(empty($query['name'])) {
                return [
                    'products' => [],
                    'pages' => [],
                ];
            }
            
            $keyword = $query['name'];
            $like = "%{$keyword}%";
            $searchPredicate = $where->nest();
            $searchPredicate->like("{$this->table}.model", $like);
            $searchPredicate->OR;
            $searchPredicate->like("{$this->table}.alias", $like);
            $searchPredicate->OR;
            $searchPredicate->like("{$this->table}.introduction", $like);
            $where->addPredicate($searchPredicate, PredicateSet::COMBINED_BY_AND);
        }
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
        if( isset($query['spec_group'])) {
            $keyword = $query['spec_group'];
            $select->join(
                $productsSpecGroupTableGateway->table,
                "{$this->table}.id={$productsSpecGroupTableGateway->table}.products_id",
                []
            );
            $where->equalTo("{$productsSpecGroupTableGateway->table}.name", $keyword);
        }
        $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->adapter);
        $select->join(
            $npClassHasProductsTableGateway->table,
            "{$this->table}.id={$npClassHasProductsTableGateway->table}.products_id",
            []
        );
        if(preg_match('/^\d+$/', $method_or_id)) {
            $where->equalTo("{$npClassHasProductsTableGateway->table}.np_class_id", $method_or_id);
        }else {
            $column = 'is_'.$method_or_id;
            if(array_search($column, $this->columns) !== false)
            {
                $where->equalTo("{$this->table}.{$column}", 1);
            }
        }
        $productsDiscountTableGateway = new ProductsDiscountTableGateway($this->adapter);
        $select->join(
            $productsDiscountTableGateway->table,
            "{$this->table}.id={$productsDiscountTableGateway->table}.products_id",
            ["discount"],
            Select::JOIN_LEFT
        );
        
        $where->lessThan("{$productsDiscountTableGateway->table}.start_date", date("Y-m-d H:i:s"));
        $where->greaterThanOrEqualTo("{$productsDiscountTableGateway->table}.end_date", date("Y-m-d H:i:s"));
        
        if(isset($query['sort']))
        {
            $sortContainer = [
                "default" => [
                    "{$this->table}.id desc",
                    "{$this->table}.viewed_count desc",
                ],
                "-id" => [
                    "{$this->table}.id desc",
                ],
                "-sale_count" => [
                    "{$this->table}.sale_count desc",
                ],
                "-viewed_count" => [
                    "{$this->table}.viewed_count desc",
                ],
                "-price" => [
                    "{$this->table}.real_price desc",
                ],
                "+price" => [
                    "{$this->table}.real_price asc",
                ],
            ];
            $sort = $query['sort'];
            $select->order($sortContainer[$sort]);
        }
        $select->where($where);
        $sqlStr = $this->sql->buildSqlString($select);
        logger()->info($sqlStr);
        $pagiAdapter = new DbSelect($select, $this->adapter);
        $paginator = new Paginator($pagiAdapter);
        $paginator->setItemCountPerPage($countPerPage);
        $query = $request->getQueryParams();
        $pageNumber = isset($query['page']) ? intval($query['page']) : 1;
        $paginator->setCurrentPageNumber($pageNumber);
        $items = $paginator->getCurrentItems();
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
        $assetsTableGateway = new AssetsTableGateway($this->adapter);
        foreach($items as &$item)
        {
            $assetsSelect = $assetsTableGateway->getSql()->select();
            $assetsSelect->columns(['id','path']);
            $assetsSelect->where([
                'table' => 'products', 'table_id' => $item['id']
            ]);
            $assetsSelect->order(['sort asc', 'id asc'])->limit(2);
             $assets = $assetsTableGateway->selectWith($assetsSelect);
             $item['assets_image'] = [];
             foreach ($assets as $asset) {
                 $item['assets_image'][] = $asset->path;
             }
             $specSelect = $productsSpecTableGateway->getSql()->select();
            $specSelect->columns([
                'sum_stock' => new Expression("SUM(`stock`)"),
            ])->where(['products_id' => $item['id']]);
            $dataSource = $productsSpecTableGateway->sql->prepareStatementForSqlObject($specSelect)->execute();
            $specResultSet = new ResultSet();
            $specResultSet->initialize($dataSource);
            $specItem = $specResultSet->current();
            if($specItem->sum_stock) {
                $item['sum_stock'] = $specItem->sum_stock;
            }else {
                $item['sum_stock'] = 0;
            }
            //affiliateLink
            $productsSpecGroupSelect = $productsSpecGroupTableGateway->getSql()->select();
            $productsSpecGroupSelect->columns([
                "spec_group_count" => new Expression("COUNT(`id`)")
            ])->where(["products_id" => $item['id']]);
            $productsSpecGroupDataSource = $productsSpecGroupTableGateway->getSql()
                ->prepareStatementForSqlObject($productsSpecGroupSelect)->execute();
            $productsSpecGroupResultSet = new ResultSet();
            $productsSpecGroupResultSet->initialize($productsSpecGroupDataSource);
            $spec_group_count = $productsSpecGroupResultSet->current()->spec_group_count;
            $item['spec_group_count'] = $spec_group_count;
        }
        $pages = $paginator->getPages();
        $pages->pagesInRange = (array)$pages->pagesInRange;
        $pages->pagesInRange = array_values($pages->pagesInRange);
        return [
            'products' => (array)$items,
            'pages' => $pages,
        ];
    }
}
