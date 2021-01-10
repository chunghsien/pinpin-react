<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\Store\TableGateway\ProductsTableGateway;
use Chopin\Store\TableGateway\NpClassTableGateway;
use Chopin\Store\TableGateway\NpClassHasProductsTableGateway;
use Chopin\Store\TableGateway\ProductsSpecTableGateway;
use Chopin\Store\TableGateway\ProductsSpecGroupTableGateway;
use Chopin\SystemSettings\TableGateway\AssetsTableGateway;
use Chopin\Store\TableGateway\ProductsIdentifyTableGateway;
use Symfony\Component\Console\Output\ConsoleOutput;
use Chopin\Store\TableGateway\ProductsDiscountTableGateway;

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
        $productsIdentifyTableGateway = new ProductsIdentifyTableGateway($this->adapter);
        $productsDiscountTableGateway = new ProductsDiscountTableGateway($this->adapter);
        $connection = $this->adapter->driver->getConnection();
        $connection->beginTransaction();
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
                $productsCloneSet['real_price'] = $productsCloneSet['price'];
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
                        if ($productsIdentifyTableGateway->select($productsIdentifySet)->count() == 0) {
                            $productsIdentifyTableGateway->insert($productsIdentifySet);
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
                                $productsSpecGroupTableGateway->insert($psGroupSetClone);
                                $lastProductsSpecGroupId = $productsSpecGroupTableGateway->lastInsertValue;
                                if (isset($psGroupSet['products_spec'])) {
                                    foreach ($psGroupSet['products_spec'] as $specSet) {
                                        $specSet['language_id'] = 119;
                                        $specSet['locale_id'] = 229;
                                        $specSet['products_id'] = $lastProductsId;
                                        $specSet['products_spec_group_id'] = $lastProductsSpecGroupId;
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
        } catch (\Exception $e) {
            $connection->rollback();
            $output = new ConsoleOutput();
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
    
    public function down()
    {
        //
    }
}
