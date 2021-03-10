<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\Store\TableGateway\NpClassHasProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Chopin\Store\TableGateway\ProductsIdentityTableGateway;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chopin\Store\TableGateway\ProductsDiscountTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupAttrsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecAttrsTableGateway;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\RowGateway\AbstractRowGateway;

class Migrate_Alter_products_20201218071140 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
     * @var string
     */
    protected $type = 'alter';
    
    
    /**
     * 
     * @var string
     */
    protected $table = 'products';
    
    protected $priority = 3;
    
    public function up()
    {
        $dataPosition = dirname(__DIR__).'/sql_seeds/products.json';
        $products = json_decode(file_get_contents($dataPosition), true);
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $npClassTableGateway = new NpClassTableGateway($this->adapter);
        $npClassHasProductsTableGateway = new NpClassHasProductsTableGateway($this->adapter);
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
        $productsSpecGroupTableGateway = new ProductsSpecGroupTableGateway($this->adapter);
        $assetsTableGateway = new AssetsTableGateway($this->adapter);
        $productsIdentityTableGateway = new ProductsIdentityTableGateway($this->adapter);
        $productsDiscountTableGateway = new ProductsDiscountTableGateway($this->adapter);
        $productsSpecGroupAttrsTableGateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
        $productsSpecAttrsTableGateway = new ProductsSpecAttrsTableGateway($this->adapter);
        $connection = $this->adapter->driver->getConnection();
        $connection->beginTransaction();
        
        $products_spec_attrs_json = file_get_contents(dirname(__DIR__).'/sql_seeds/products_spec_attrs.json');
        $products_spec_attrs_data = json_decode($products_spec_attrs_json, true);
        $productsSpecAttrsTableGateway = new ProductsSpecAttrsTableGateway($this->adapter);
        foreach ($products_spec_attrs_data as $set) {
            $set['language_id'] = 119;
            $set['locale_id'] = 229;
            $productsSpecAttrsTableGateway->insert($set);
        }
        $products_spec_group_attrs_json = file_get_contents(dirname(__DIR__).'/sql_seeds/products_spec_group_attrs.json');
        $products_spec_group_attrs_data = json_decode($products_spec_group_attrs_json, true);
        $productsSpecGroupAttrsTableGateway = new ProductsSpecGroupAttrsTableGateway($this->adapter);
        foreach ($products_spec_group_attrs_data as $set) {
            $set['language_id'] = 119;
            $set['locale_id'] = 229;
            $productsSpecGroupAttrsTableGateway->insert($set);
        }
        try {
            foreach ($products as $productsSet) {
                $productsCloneSet = $productsSet;
                $productsCloneSet['language_id'] = 119;
                $productsCloneSet['locale_id'] = 229;
                unset($productsCloneSet['np_class']);
                unset($productsCloneSet['products_spec_group']);
                unset($productsCloneSet['assets']);
                unset($productsCloneSet['products_identify']);
                $discount = $productsCloneSet['discount'];
                unset($productsCloneSet['discount']);
                $price = intval($productsCloneSet['price'])*30;
                $productsCloneSet['price'] = $price;
                $productsCloneSet['real_price'] = $price;
                if ($productsTableGateway->select($productsCloneSet)->count() === 0) {
                    $productsTableGateway->insert($productsCloneSet);
                    $lastProductsId = $productsTableGateway->lastInsertValue;
                    if($discount > 0) {
                        $productsDiscountSet = [
                            "products_id" => $lastProductsId,
                            "discount" => $discount,
                            "start_date" => "2020-12-01 00:00:01",
                            "end_date" => "2021-12-31 23:59:59",
                        ];
                        $productsDiscountTableGateway->insert($productsDiscountSet);
                    }
                    if(isset($productsSet['products_identify'])) {
                        $productsIdentifySet = $productsSet['products_identify'];
                        $productsIdentifySet['products_id'] = $lastProductsId;
                        if ($productsIdentityTableGateway->select($productsIdentifySet)->count() == 0) {
                            $productsIdentityTableGateway->insert($productsIdentifySet);
                        }
                    }
                    foreach ($productsSet['np_class'] as $npName) {
                        $select = $npClassTableGateway->getSql()->select();
                        $where = $select->where;
                        $where->expression("lower(`name`) = lower(?)", [strtolower($npName)]);
                        $select->where($where);
                        $npRow = $npClassTableGateway->selectWith($select)->current();
                        if($npRow) {
                            $npHasProductsSet = [
                                'np_class_id' => $npRow->id,
                                'products_id' => $lastProductsId,
                            ];
                            $npClassHasProductsTableGateway->delete($npHasProductsSet);
                            $npClassHasProductsTableGateway->insert($npHasProductsSet);
                        }
                    }
                    if (isset($productsSet['products_spec_group'])) {
                        foreach ($productsSet['products_spec_group'] as $psGroupSet) {
                            $psGroupSetClone = $psGroupSet;
                            $psGroupSetClone['products_id'] = $lastProductsId;
                            $psGroupSetClone['language_id'] = 119;
                            $psGroupSetClone['locale_id'] = 229;
                            if (isset($psGroupSet['products_spec'])) {
                                unset($psGroupSetClone['products_spec']);
                            }
                            if ($productsSpecGroupTableGateway->select($psGroupSetClone)->count() === 0) {
                                $productsSpecGroupAttrsRow = $productsSpecGroupAttrsTableGateway->select(['name' => $psGroupSetClone['name']])->current();
                                $psGroupSetClone['products_spec_group_attrs_id'] = $productsSpecGroupAttrsRow->id;
                                $productsSpecGroupTableGateway->insert($psGroupSetClone);
                                $lastProductsSpecGroupId = $productsSpecGroupTableGateway->lastInsertValue;
                                if (isset($psGroupSet['products_spec'])) {
                                    foreach ($psGroupSet['products_spec'] as $specSet) {
                                        $specSet['language_id'] = 119;
                                        $specSet['locale_id'] = 229;
                                        $specSet['products_id'] = $lastProductsId;
                                        $specSet['products_spec_group_id'] = $lastProductsSpecGroupId;
                                        $productsSpecAttrsRow = $productsSpecAttrsTableGateway->select(['name' => $specSet['name']])->current();
                                        $specSet['products_spec_attrs_id'] = $productsSpecAttrsRow->id;
                                        $productsSpecTableGateway->insert($specSet);
                                    }
                                }
                            }
                        }
                    }
                    if (isset($productsSet['assets'])) {
                        foreach ($productsSet['assets'] as $assetsSet) {
                            $assetsSet['table_id'] = $lastProductsId;
                            $assetsSet['table'] = $productsTableGateway->getTailTableName();
                            $assetsSet['language_id'] = 119;
                            $assetsSet['locale_id'] = 229;
                            $assetsSet['mime'] = '';
                            if ($assetsTableGateway->select($assetsSet)->count() == 0) {
                                $assetsTableGateway->insert($assetsSet);
                            }
                        }
                    }
                }
            }
            $connection->commit();
            for($i=0 ; $i < 3 ; $i++)
            {
                $rand = rand(32,41);
                switch ($i) {
                    case 0:
                        //new
                        $this->updateStatus($productsTableGateway, $rand, 'is_new');
                        break;
                    case 1:
                        //hot
                        $this->updateStatus($productsTableGateway, $rand, 'is_hot');
                        break;
                    case 2:
                        //recommend
                        $this->updateStatus($productsTableGateway, $rand, 'is_recommend');
                        break;
                }
            }
            
        } catch (\Exception $e) {
            $connection->rollback();
            $output = new ConsoleOutput();
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
    
    protected function updateStatus(AbstractTableGateway $tableGateway, $rand, $column)
    {
        for ($j=0 ; $j < $rand ; $j++)
        {
            $select = $tableGateway->getSql()->select();
            $where = $select->where;
            $where->equalTo($column, 0);
            $select->order(new Expression("RAND()"));
            $select->limit(1);
            $select->where($where);
            $row = $tableGateway->selectWith($select)->current();
            if($row instanceof AbstractRowGateway) {
                $row->{$column} = 1;
                $row->save();
            }
        }
    }
    public function down()
    {
        //
    }
}
