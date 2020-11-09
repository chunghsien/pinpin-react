<?php

namespace Chopin\Store\Service;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Where;
use Chopin\Store\TableGateway\LogisticsGlobalTableGateway;
use Chopin\Store\TableGateway\CartTableGateway;
use Chopin\Store\TableGateway\CouponTableGateway;
use Chopin\Store\TableGateway\OrderTableGateway;
use Chopin\Store\TableGateway\OrderDetailTableGateway;
use Chopin\Store\TableGateway\OrderHasCouponTableGateway;
use Chopin\Support\Registry;
use Laminas\ServiceManager\ServiceManager;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\Db\Sql\Predicate\Operator;
use Chopin\HttpMessage\Response\AttributeTemplate\Error;
use Chopin\Users\TableGateway\UsersTableGateway;
use Chopin\Users\TableGateway\UsersProfileTableGateway;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\Sql\Expression;
use Mezzio\Session\SessionPersistenceInterface;
use Chopin\LaminasDb\DB;


class OrderService
{

    /**
     *
     * @var LogisticsGlobalTableGateway
     */
    protected $logisticsGlobalTableGateway;

    /**
     *
     * @var CartTableGateway
     */
    protected $cartTableGateway;

    /**
     *
     * @var CouponTableGateway
     */
    protected $couponTableGateway;

    /**
     *
     * @var OrderTableGateway
     */
    protected $orderTableGateway;

    /**
     *
     * @var OrderDetailTableGateway
     */
    protected $orderDetailTableGateway;

    /**
     *
     * @var OrderHasCouponTableGateway
     */
    protected $orderHasCouponTableGateway;

    private $logisticsFee;

    private $releation_table;

    /**
     *
     * @var AbstractTableGateway
     */
    protected $releationProductsTableGateway;

    /**
     *
     * @var UsersTableGateway
     */
    protected $usersTablegateway;

    /**
     *
     * @var UsersProfileTableGateway
     */
    protected $usersProfileTableGateway;

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    public function __construct(Adapter $adapter, $releation_table = 'products_spec')
    {
        $this->adapter = $adapter;
        $this->releation_table = $releation_table;
        $this->logisticsGlobalTableGateway = new LogisticsGlobalTableGateway($adapter);
        $this->cartTableGateway = new CartTableGateway($adapter);
        $this->couponTableGateway = new CouponTableGateway($adapter);
        $this->orderTableGateway = new OrderTableGateway($adapter);
        $this->orderDetailTableGateway = new OrderDetailTableGateway($adapter);
        $this->orderHasCouponTableGateway = new OrderHasCouponTableGateway($adapter);
        $this->usersTablegateway = new UsersTableGateway($adapter);
        $this->usersProfileTableGateway = new UsersProfileTableGateway($adapter);
        $this->releationProductsTableGateway = AbstractTableGateway::newInstance($releation_table, $adapter);
    }

    /**
     *
     * @param int $user_id
     * @return NULL[]|\ArrayObject[]|array[]
     */
    public function getOrderUser($user_id)
    {
        $datas = DB::selectFactory([
            'from' => $this->usersProfileTableGateway->table,
            'where' => [
                ['equalTo', 'and', ['users_id', $user_id]],
            ],
        ])->toArray();
        $values = [];
        foreach ($datas as $data) {
            $key = $data['key'];
            if (isset($data['aes_value']) && $data['aes_value']) {
                $value = $data['aes_value'];
            } else {
                $value = $data['value'];
            }
            $values[$key] = $value;
        }
        return $values;
    }

    /**
     *
     * @param int $logistics_global_id
     * @return RowGatewayInterface
     */
    public function getOrderLogistics($logistics_global_id)
    {
        return DB::selectFactory([
            'from' => $this->logisticsGlobalTableGateway->table,
            'where' => [
                ['equalTo', 'and', ['id', '=', $logistics_global_id]],
            ],
        ])->current();
    }

    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function cleanCart()
    {
        $guest_serial_data = $this->cartTableGateway->getGuestSerial();
        $where = new Where();
        $where->equalTo('guest_serial', $guest_serial_data['guest_serial']);
        return $this->cartTableGateway->delete($where);
    }

    /**
     *
     * @param array $data
     * @return \Laminas\Db\Adapter\Driver\ResultInterface|boolean|array
     */
    public function saveOrder($data)
    {
        /**
         *
         * @var \Laminas\Db\Adapter\Driver\Pdo\Connection $connection
         */
        $connection = $this->adapter->driver->getConnection();
        if (isset($data['id'])) {
            $connection->beginTransaction();
            try {
                $id = $data['id'];
                unset($data['id']);
                $values = $data;
                $this->orderTableGateway->update(['id' => $id], $values);
                if (isset($values['status'])) {
                    $this->orderDetailTableGateway->update(['order_id' => $id], [
                        'status' => $values['status'],
                        'deleted_at' => new Expression('NULL'),
                    ]);
                }
                $connection->commit();
            } catch (\Exception $e) {
                TryCatchTransToLog($e);
                $connection->rollback();
            }
        } else {
            $orderData = $this->buildOrderData($data);
            //debug($this->cartTableGateway->getCarts()->toArray());
            $connection->beginTransaction();
            try {
                $orderData['deleted_at'] = date("Y-m-d H:i:s");
                $result = $this->orderTableGateway->insert($orderData);
                $orderDetailDatas = $this->buildOrderDetialData($result->getGeneratedValue());
                $this->orderDetailTableGateway->insert($orderDetailDatas);

                $discount = floatval($orderData['discount']);
                if ($discount) {
                    $status = $this->saveOrderCoupon($result->getGeneratedValue());
                    if ($status) {
                        $connection->commit();
                    } else {
                        $connection->rollback();
                        return false;
                    }
                } else {
                    $connection->commit();
                }
            } catch (\Exception $e) {
                TryCatchTransToLog($e);
                $connection->rollback();
            }
            return array_merge(['id' =>$result->getGeneratedValue()], $orderData);
            //return $mainData;
        }
    }

    /**
     *
     * @param int $order_id
     */
    protected function saveOrderCoupon($order_id)
    {
        /**
         *
         * @var ServiceManager $serviceManager
         */
        $serviceManager = Registry::get(ServiceManager::class);

        $verify = $serviceManager->has(SessionPersistenceInterface::class);
        $couponContainer = [];
        if ($verify) {
            $session = $serviceManager->get(SessionPersistenceInterface::class);
            $couponContainer = $session->get('coupon');
        } else {
            $couponContainer =  $_SESSION['coupon'];
        }
        try {
            foreach ($couponContainer as $coupon) {
                $this->orderHasCouponTableGateway->insert([
                    'order_id' => $order_id,
                    'coupon_id' => $coupon['id'],
                ]);
            }
        } catch (\Exception $e) {
            TryCatchTransToLog($e);
            return false;
        }
        return true;
    }

    public function calCoupon($coupons, $subtotal)
    {
        return $this->couponTableGateway->calCoupon($coupons, $subtotal);
    }

    /**
     *
     * @return boolean
     */
    public function isFreeShippingFee()
    {
        $freeShippingFee = $this->couponTableGateway->getGlobalFreeShippingFee($this->getSubtotal());
        return $freeShippingFee === 0 ? true : false;
    }
    /**
     *
     * @param string $prefix
     * @return string
     */
    public function buildOrderSerial($prefix = 'PP')
    {
        return $this->orderTableGateway->buildOrderSerial($prefix);
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function buildOrderData($data)
    {
        return $this->orderTableGateway->buildData($data);
    }

    /**
     *
     * @return number
     */
    public function getSubtotal()
    {
        $subtotalArr = $this->cartTableGateway->subTotal($this->releation_table);
        return floatval($subtotalArr['data']['subtotal']);
    }

    /**
     *
     * @param int $order_id
     * @return \Laminas\Db\ResultSet\ResultSetInterface
     */
    public function getOrderDetialData($order_id)
    {
        $pt = AbstractTableGateway::$prefixTable;
        return $this->orderDetailTableGateway->selectFactory([
            'where' => [
                [Operator::class, 'AND', ['order_id', '=', $order_id]],
            ],
            'join' => [
                [
                    $pt.'products_spec',
                    $pt.'order_detail.products_id='.$pt.'products_spec.id',
                    ['name', 'extra_name', 'triple_name', 'barcode', 'serial'],
                ],
            ],
        ]);
    }

    /**
     *
     * @param integer $order_id
     * @return array
     */
    public function buildOrderDetialData($order_id)
    {
        $subtotalArr = $this->cartTableGateway->subTotal($this->releation_table);
        $items = $subtotalArr['data']['items'];
        $detailData = [];
        $columns = ['id', 'quantity'];
        if ($this->releationProductsTableGateway->getTailTableName() == 'products') {
            $columns[] = 'model';
        } else {
            $columns = array_merge($columns, ['name', 'extra_name', 'triple_name']);
        }
        foreach ($items as $item) {
            $price = floatval($item['real_price']);
            if ( ! $price) {
                $price = floatval($item['products_real_price']);
            }
            $quantity = intval($item['quantity']);
            $scripts = [
                'from' => $this->releationProductsTableGateway->table,
                'columns' => $columns,
                'where' => [
                    ['equalTo', 'and', ['id', $item['products_id']]],
                ],
            ];
            $releationProductsItem = DB::selectFactory($scripts)->current();
            if (intval($releationProductsItem['quantity']) < intval($quantity)) {
                $message = $item['model'];
                $tailRoundBrackets = '';
                if (isset($releationProductsItem['name'])) {
                    $message.= ' ('.$releationProductsItem['name'];
                }
                if (isset($releationProductsItem['extra_name'])) {
                    $message.=  ', '.$releationProductsItem['extra_name'];
                    $tailRoundBrackets = ' )';
                }
                if (isset($releationProductsItem['triple_name'])) {
                    $message.= ', '.$releationProductsItem['triple_name'];
                }
                if ($tailRoundBrackets) {
                    $message.= $tailRoundBrackets;
                }
                $message.= ' 庫存量不足。';
                return new Error($message, []);
            }
            $_ = [
                'order_id' => $order_id,
                'products_id' => $item['products_id'],
                'quantity' => $quantity,
                'model' => $item['model'],
                'price' => $price,
                'subtotal' => $quantity * $price,
            ];
            $detailData[] = $_;
        }
        return $detailData;
    }

    /**
     * *取得運費
     * @param int $logistics_id, logistics表的id
     * @param int $good_id, 商品另行設定的id
     * @return number
     */
    public function getLogisticsFee($logistics_id, $good_id = 0)
    {
        $logisticsFee = null;
        if ($this->logisticsFee) {
            $logisticsFee = $this->logisticsFee;
        } else {
            $where = new Where();
            $where->equalTo('id', $logistics_id);
            $where->equalTo('is_use', 1);
            //debug(floatval($this->logisticsGlobalTableGateway->select($where)->current()->price));
            $logisticsFee = floatval($this->logisticsGlobalTableGateway->select($where)->current()->price);
            $this->logisticsFee = $logisticsFee;
        }
        if ($this->isFreeShippingFee) {
            return 0;
        } else {
            return $logisticsFee;
        }
    }
}
