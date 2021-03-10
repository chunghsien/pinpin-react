<?php
namespace App\Service\Site;

use Laminas\Db\Adapter\Adapter;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\Store\TableGateway\ProductsDiscountTableGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\ResultSet\ResultSet;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Laminas\Db\Sql\Select;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Chopin\LaminasDb\RowGateway\RowGateway;
use phpDocumentor\Reflection\Types\Parent_;
use Chopin\Store\TableGateway\ProductsSpecGroupAttrsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecAttrsTableGateway;

class ProductsService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;
    
    
    /*
     * @var ProductsTableGateway
     */
    protected $productsTableGateway;

    /**
     *
     * @var ProductsDiscountTableGateway
     */
    protected $productsDiscountTableGateway;

    /**
     *
     * @var NpClassTableGateway
     */
    protected $npClassTableGateway;

    /**
     *
     * @var ProductsSpecTableGateway
     */
    protected $productsSpecTableGateway;

    /**
     *
     * @var ProductsSpecAttrsTableGateway
     */
    protected $productsSpecAttrsTableGateway;

    /**
     * 
     * @var ProductsSpecGroupTableGateway
     */
    protected $productsSpecGroupTableGateway;

    /**
     *
     * @var ProductsSpecGroupAttrsTableGateway
     */
    protected $productsSpecGroupAttrsTableGateway;
    

    /**
     *
     * @var AssetsTableGateway
     */
    protected $assetsTableGateway;

    
    public function __construct(Adapter $adapter, ServerRequestInterface $request)
    {
        $this->productsTableGateway = new ProductsTableGateway($adapter);
        $this->npClassTableGateway = new NpClassTableGateway($adapter);
        $this->productsDiscountTableGateway = new ProductsDiscountTableGateway($adapter);
        $this->productsSpecTableGateway = new ProductsSpecTableGateway($adapter);
        $this->productsSpecAttrsTableGateway = new ProductsSpecAttrsTableGateway($adapter);
        $this->assetsTableGateway = new AssetsTableGateway($adapter);
        $this->productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($adapter);
        $this->productsSpecGroupAttrsTableGateway = new ProductsSpecGroupAttrsTableGateway($adapter);
        parent::__construct($adapter, $request);
    }

    protected function addAssetsImages($resultSet, $isChainSpecGroup=true)
    {
        foreach ($resultSet as &$item) {
            $assetsSelect = $this->assetsTableGateway->getSql()->select();
            $assetsSelect->columns([
                'id',
                'path'
            ]);
            $predicate = $assetsSelect->where;
            $predicate->like('table', "%{$this->productsTableGateway->getTailTableName()}%");
            $predicate->equalTo('table_id', $item['id']);
            $predicate->equalTo('language_id', $this->localeRow->language_id);
            $predicate->equalTo('locale_id', $this->localeRow->locale_id);
            $tmpResultSet = $this->assetsTableGateway->selectWith($assetsSelect);
            $assetsImages = [];
            foreach ($tmpResultSet as $thumb) {
                $assetsImages[] = $thumb->path;
            }
            $item['assets_image'] = $assetsImages;
            if(empty($item['spec_groups']) && $isChainSpecGroup) {
                $spec_groups = $this->addProductsSpecGroup($item['id']);
                $item['spec_groups'] = $spec_groups;
                $stock = 0;
                foreach ($spec_groups as $group)
                {
                    $stock += $group['stock'];
                }
                $item['stock'] = $stock;
            }
        }
        return $resultSet;
    }
    
    protected function addProductsSpecGroup($products_id)
    {
        //$this->productsSpecGroupTableGateway->select();
        $productsSpecGroupTableGateway = $this->productsSpecGroupTableGateway;
        $productsSpecGroupAttrsTableGateway = $this->productsSpecGroupAttrsTableGateway;
        $select = $this->productsSpecGroupTableGateway->getSql()->select();
        $select->columns(['id', 'sale_count']);
        $productsSpecGroupAttrsTableGateway = $this->productsSpecGroupAttrsTableGateway;
        $select->join(
            $productsSpecGroupAttrsTableGateway->table,
            "{$productsSpecGroupTableGateway->table}.products_spec_group_attrs_id={$productsSpecGroupAttrsTableGateway->table}.id",
            ["name", "extra_name", "image"]
        );
        $predicate = $select->where;
        $predicate->isNull("{$productsSpecGroupTableGateway->table}.deleted_at");
        $predicate->isNull("{$productsSpecGroupAttrsTableGateway->table}.deleted_at");
        $predicate->equalTo("{$productsSpecGroupTableGateway->table}.products_id", $products_id);
        $predicate->equalTo("{$productsSpecGroupAttrsTableGateway->table}.language_id", $this->localeRow->language_id);
        $predicate->equalTo("{$productsSpecGroupAttrsTableGateway->table}.locale_id", $this->localeRow->locale_id);
        
        $select->where($predicate);
        $select->order([
            "{$productsSpecGroupTableGateway->table}.sort asc",
            "{$productsSpecGroupTableGateway->table}.id desc"
        ]);
        $resultSet = $this->productsSpecGroupTableGateway->selectWith($select);
        //Chopin\Store\RowGateway\ProductsSpecGroupRowGateway
        $result = [];
        $productsSpecTableGateway = $this->productsSpecTableGateway;
        $productsSpecAttrsTableGateway = $this->productsSpecAttrsTableGateway;
        foreach ($resultSet as $row)
        {
            $specResultSet = [];
            $stock = 0;
            $specSelect = $productsSpecTableGateway->getSql()->select();
            $specSelect->join(
                $productsSpecAttrsTableGateway->table,
                "{$productsSpecTableGateway->table}.products_spec_attrs_id={$productsSpecAttrsTableGateway->table}.id",
                ["name", "extra_name", "triple_name"]
            );
            $specPredicate = $specSelect->where;
            $specPredicate->isNull("{$productsSpecTableGateway->table}.deleted_at");
            $specPredicate->isNull("{$productsSpecAttrsTableGateway->table}.deleted_at");
            $specPredicate->equalTo("{$productsSpecTableGateway->table}.products_spec_group_id", $row->id);
            $specPredicate->equalTo("{$productsSpecAttrsTableGateway->table}.language_id", $this->localeRow->language_id);
            $specPredicate->equalTo("{$productsSpecAttrsTableGateway->table}.locale_id", $this->localeRow->locale_id);
            $specSelect->where($specPredicate);
            $specResultSet = $this->productsSpecTableGateway->selectWith($specSelect)->toArray();
            
            foreach ($specResultSet as $spec)
            {
                $stock += $spec['stock'];
            }
            
            /**
             * @var \Chopin\Store\RowGateway\ProductsSpecGroupRowGateway $row
             */
            $row->with('specs', $specResultSet);
            $row = $row->toArray();
            $row['stock'] = $stock;
            $result[] = $row;
        }
        return $result;
    }
    protected function addDiscount($resultSet)
    {
        $discountIds = [];
        foreach ($resultSet as $item) {
            $discountIds[] = $item['id'];
        }
        $discountsResultset = $this->productsDiscountTableGateway->select([
            'products_id' => $discountIds
        ]);
        foreach ($discountsResultset as $discount) {
            foreach ($resultSet as &$item) {
                if ($discount['products_spec_id'] == 0 && $item['id'] == $discount['products_id']) {
                    $discountPrice = floatval($item['real_price']) * ((100 - floatval($discount['discount'])) / 100);
                    $item['discount_price'] = ceil($discountPrice);
                    $item['discount'] = $discount['discount'];
                }
            }
        }
        return $resultSet;
    }
    
    protected function buildSpec(Select $select)
    {
        $select->join(
            $this->productsSpecGroupTableGateway->table,
            "{$this->productsTableGateway->table}.id={$this->productsSpecGroupTableGateway->table}.products_id",
            []
        );
        $select->join(
            $this->productsSpecTableGateway->table,
            "{$this->productsTableGateway->table}.id={$this->productsSpecTableGateway->table}.products_id",
            []
        );
    }
    /**
     *
     * @param string $category
     * @param integer $limit
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getNewProducts($category = null, $limit = 10)
    {
        $pt = AbstractTableGateway::$prefixTable;
        $select = $this->npClassTableGateway->getSql()->select();
        if ($category) {
            $select->columns([
                'category' => 'name'
            ]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id");
        } else {
            $select->columns([]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id", []);
        }
        $select->quantifier("DISTINCT");
        $select->join($this->productsTableGateway->table, "{$pt}np_class_has_products.products_id={$this->productsTableGateway->table}.id");
        $select->limit($limit);
        $predicate = $select->where;
        if ($category) {
            $col = "{$this->npClassTableGateway->table}.name";
            $predicate->equalTo($col, $category);
        }
        $predicate->isNull("{$this->npClassTableGateway->table}.deleted_at");
        $predicate->isNull("{$this->productsTableGateway->table}.deleted_at");
        $predicate->equalTo('is_new', 1);
        $predicate->equalTo("{$this->npClassTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->npClassTableGateway->table}.locale_id", $this->localeRow->locale_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.locale_id", $this->localeRow->locale_id);
        
        
        $select->where($predicate);
        $select->order("{$this->productsTableGateway->table}.id DESC");
        $select->order("{$this->productsTableGateway->table}.sort ASC");
        $this->buildSpec($select);
        $resultSet = $this->npClassTableGateway->selectWith($select)->toArray();
        $resultSet = $this->addAssetsImages($resultSet);
        $resultSet = $this->addDiscount($resultSet);
        $resultSet = $this->addLocale($resultSet, 'product', 'id');
        //$resultSet = $this->addProductsSpecGroup($products_id)
        return $resultSet;
    }

    /**
     *
     * @param string $category
     * @param integer $limit
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getPopularProducts($category = null, $limit = 10)
    {
        $pt = AbstractTableGateway::$prefixTable;
        $select = $this->npClassTableGateway->getSql()->select();
        if ($category) {
            $select->columns([
                'category' => 'name'
            ]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id");
        } else {
            $select->columns([]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id", []);
        }
        $select->quantifier("DISTINCT");
        $select->join($this->productsTableGateway->table, "{$pt}np_class_has_products.products_id={$this->productsTableGateway->table}.id");
        $select->limit($limit);
        $predicate = $select->where;
        if ($category) {

            $col = "{$this->npClassTableGateway->table}.name";
            $predicate->equalTo($col, $category);
        }
        $predicate->equalTo("{$this->npClassTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->npClassTableGateway->table}.locale_id", $this->localeRow->locale_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.locale_id", $this->localeRow->locale_id);
        $predicate->isNull("{$this->npClassTableGateway->table}.deleted_at");
        $predicate->isNull("{$this->productsTableGateway->table}.deleted_at");
        $select->where($predicate);
        $select->order("{$this->productsTableGateway->table}.sale_count DESC");
        $this->buildSpec($select);
        $resultSet = $this->npClassTableGateway->selectWith($select)->toArray();
        $resultSet = $this->addAssetsImages($resultSet);
        $resultSet = $this->addDiscount($resultSet);
        $resultSet = $this->addLocale($resultSet, 'product', 'id');
        return $resultSet;
    }

    /**
     *
     * @param string $category
     * @param integer $limit
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getSaleProducts($category = null, $limit = 10)
    {
        $pt = AbstractTableGateway::$prefixTable;
        $select = $this->npClassTableGateway->getSql()->select();
        if ($category) {
            $select->columns([
                'category' => 'name'
            ]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id");
        } else {
            $select->columns([]);
            $select->join("{$pt}np_class_has_products", "{$this->npClassTableGateway->table}.id={$pt}np_class_has_products.np_class_id", []);
        }
        $select->quantifier("DISTINCT");
        $select->join($this->productsTableGateway->table, "{$pt}np_class_has_products.products_id={$this->productsTableGateway->table}.id");
        $select->join($this->productsDiscountTableGateway->table, "{$this->productsTableGateway->table}.id={$this->productsDiscountTableGateway->table}.products_id", [
            "discount",
            "products_spec_id"
        ]);
        $select->limit($limit);
        $this->buildSpec($select);
        $randSeedSelect = $this->productsDiscountTableGateway->getSql()->select();
        $randSeedSelect->join(
            $this->productsTableGateway->table,
            "{$this->productsDiscountTableGateway->table}.products_id={$this->productsTableGateway->table}.id",
            []
        );
        $randSeedSelect->quantifier("distinct");
        $randSeedSelect->columns(['id']);
        $runSeedWhere = $randSeedSelect->where;
        $runSeedWhere->equalTo("{$this->productsTableGateway->table}.language_id" , $this->localeRow->language_id);
        $runSeedWhere->equalTo("{$this->productsTableGateway->table}.locale_id", $this->localeRow->locale_id);
        
        $runSeedWhere->isNull("{$this->productsDiscountTableGateway->table}.deleted_at");
        $runSeedWhere->lessThanOrEqualTo("{$this->productsDiscountTableGateway->table}.start_date", date("Y-m-d H:i:s"));
        $runSeedWhere->greaterThanOrEqualTo("{$this->productsDiscountTableGateway->table}.end_date", date("Y-m-d H:i:s"));
        $randSeedSelect->where($runSeedWhere);
        $dataSource = $this->productsDiscountTableGateway->getSql()
            ->prepareStatementForSqlObject($randSeedSelect)
            ->execute();
        $randSeedResultSet = new ResultSet();
        $randSeedResultSet->initialize($dataSource);
        $ids = [];
        foreach ($randSeedResultSet->toArray() as $item) {
            $ids[] = $item['id'];
        }
        shuffle($ids);
        if (count($ids) > $limit) {
            $ids = array_slice($ids, 0, $limit);
        }
        // random_int($min, $max)
        $predicate = $select->where;
        $predicate->equalTo("{$this->npClassTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->npClassTableGateway->table}.locale_id", $this->localeRow->locale_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.language_id" , $this->localeRow->language_id);
        $predicate->equalTo("{$this->productsTableGateway->table}.locale_id", $this->localeRow->locale_id);
        $predicate->in("{$this->productsDiscountTableGateway->table}.id", $ids);
        $predicate->isNull("{$this->npClassTableGateway->table}.deleted_at");
        $predicate->isNull("{$this->productsTableGateway->table}.deleted_at");
        $resultSet = $this->npClassTableGateway->selectWith($select)->toArray();

        // for 台灣的習慣
        foreach ($resultSet as &$item) {
            if ($item['products_spec_id'] == 0) {
                $discountPrice = floatval($item['real_price']) * ((100 - floatval($item['discount'])) / 100);
                $item['discount_price'] = ceil($discountPrice);
            }
        }
        $resultSet = $this->addAssetsImages($resultSet);
        $resultSet = $this->addLocale($resultSet, 'product', 'id');
        return $resultSet;
        
    }
    
    public function getFarCategories($limit = 0)
    {
        
    }

    public function getMidCategories($limit = 0)
    {
        
    }
    
    /**
     * 
     * @param boolean $rand
     * @param number $limit
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getNearCategories($rand = false, $limit = 0)
    {
        $select = $this->npClassTableGateway->getSql()->select();
        $where = $select->where;
        $where->equalTo('language_id', $this->localeRow->language_id);
        $where->equalTo('locale_id', $this->localeRow->locale_id);
        $where->isNull('deleted_at');
        if($rand) {
            $select->order(new Expression('RAND()'));
        }else {
            $select->order(['sort asc', 'id asc']);
        }
        $select->where($where);
        $resultSet = $this->npClassTableGateway->selectWith($select)->toArray();
        $lang = str_replace('_', '-', $this->localeRow->code);
        foreach ($resultSet as &$item)
        {
            $uri = "/{$lang}/category/{$item['id']}";
            $item["uri"] = $uri;
        }
        return $resultSet;
    }
    
}