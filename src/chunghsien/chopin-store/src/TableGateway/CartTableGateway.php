<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LaminasDb\DB\Select;
use Laminas\Db\Sql\Where;
use Chopin\LaminasDb\DB\Delete;
use Chopin\HttpMessage\Response\AttributeTemplate\Success;
use Chopin\HttpMessage\Response\AttributeTemplate\Error;
use Laminas\Db\Sql\Sql;

class CartTableGateway extends AbstractTableGateway
{
    public static $isRemoveRowGatewayFeature = false;
    
    /**
     *
     * @inheritdoc
     */
    protected $table = 'cart';

    /**
     **取出購物車內容並計算價錢(不含運費)
     * @param string $releation_table
     * @return array
     */
    public function subTotal($releation_table='products_spec')
    {
        try {
            $pt = self::$prefixTable;
            $serialData = $this->getGuestSerial();
            $where = new Where();
            $where->equalTo('guest_serial', $serialData['serial']);
            $where->greaterThan('expire', strtotime('now'));
            $select = new Select($this);
            //$select->columns(['id', guest]);
            $select->where($where);
            $select->order($pt.'cart.created_at DESC');
            $releation_table = preg_replace('/^'.$pt.'/', '', $releation_table);
            $on = $this->table.'.products_id='.$pt.$releation_table.'.id';
            $columns = [];
            if ($releation_table == 'products') {
                $columns = [
                    'products_id' => 'id',
                    'model', 'price', 'real_price',
                    'stock' => 'quantity',
                ];
            }
            if ($releation_table == 'products_spec') {
                $columns = [
                    'products_spec_id' => 'id',
                    'name',
                    'extra_name',
                    'triple_name',
                    'photo',
                    'price',
                    'real_price',
                    'stock' => 'quantity',
                ];
            }
            if ($releation_table == 'products') {
                //再處理圖片，以後有案例再處裡了
            }
            $select->join($pt.$releation_table, $on, $columns);
            if ($releation_table == 'products_spec') {
                $select->join(
                    $pt.'products',
                    $pt.'products.id='.$pt.'products_spec.products_id',
                    [
                        'products_price' => 'price',
                        'products_real_price' => 'real_price',
                        'model',
                    ]
                );
                //單一商品的促銷折扣，以後再規劃，現在沒有要做。
            }
            $select->join($pt.'np_class_has_products', $pt.'products.id='.$pt.'np_class_has_products.products_id', [], 'left');
            $select->join($pt.'np_class', $pt.'np_class.id='.$pt.'np_class_has_products.np_class_id', ['np_class_name' => 'name'], 'left');

            $result = $select->get()->toArray();
            $count = count($result);
            $subtotal = 0;

            foreach ($result as $row) {
                if (intval($row['real_price'])) {
                    $subtotal += (floatval($row['real_price']) * floatval($row['quantity']));
                //continue;
                } elseif ($row['products_real_price']) {
                    $subtotal += (floatval($row['products_real_price']) * floatval($row['quantity']));
                }
            }
            return (new Success('success', [
                'items' => $result,
                'subtotal' => $subtotal,
                'count' => $count,
            ]))->__toArray();
        } catch (\Exception $e) {
            TryCatchTransToLog($e);
            return (new Error('fail', [
                'items' => [],
                'subtotal' => 0,
                'count' => 0,
            ]))->__toArray();
        }
        return (new Success('success', [
            'items' => [],
            'subtotal' => 0,
            'count' => 0,
        ]))->__toArray();
    }

    protected function stockVerify($products_id, $quantity, $releation_table="products_spec")
    {
        $adapter = $this->getAdapter();
        $sql = new Sql($adapter);
        $releation_table = str_replace(self::$prefixTable, '', $releation_table);
        $releation_table = self::$prefixTable.$releation_table;
        $select = $sql->select($releation_table)->columns(['id', 'quantity'])->where(['id' => $products_id]);
        $stock = $sql->prepareStatementForSqlObject($select)->execute()->current();
        return intval($stock['quantity']) >= intval($quantity);
    }
    public function updateToCart($products_id, $quantity=1, $releation_table="products_spec")
    {
        try {
            if ($this->stockVerify($products_id, $quantity, $releation_table)) {
                $serialData = $this->getGuestSerial();
                $where = new Where();
                $where->equalTo('products_id', $products_id);
                //$where->equalTo('guest_serial', $serialData['serial']);
                $this->update([
                    'products_id' => $products_id,
                    'quantity' => $quantity,
                    'expire' => $serialData['expire'],
                ], $where);
                return (new Success('', []))->__toArray();
            } else {
                return (new Error('購買數量超過庫存數量', []))->__toArray();
            }
        } catch (\Exception $e) {
            TryCatchTransToLog($e);
            return (new Error('購物車更新失敗', []))->__toArray();
        }
    }

    public function deleteToCart($products_id)
    {
        try {
            $serialData = $this->getGuestSerial();
            $where = new Where();
            $where->equalTo('products_id', $products_id);
            $where->equalTo('guest_serial', $serialData['serial']);
            $this->delete($where);
            return (new Success('', []))->__toArray();
        } catch (\Exception $e) {
            TryCatchTransToLog($e);
            return (new Error('購物車刪除失敗', []))->__toArray();
        }
    }

    /**
     * 如果products_spec表有紀錄的話這邊的products_id實際上指的是products_spec.id
     * @param int $products_id
     * @param int $quantity
     * @return array
     */
    public function addToCart($products_id, $quantity=1, $releation_table='products_spec')
    {
        try {
            $serialData = $this->getGuestSerial();
            $select = new Select($this);
            $where = new Where();
            $where->equalTo('guest_serial', $serialData['serial']);
            $where->equalTo('products_id', $products_id);
            //$where->greaterThan('expire', $serialData['expire']);
            $resultSet = $select->where($where)->get();
            if ($resultSet->count()==0) {
                if ($this->stockVerify($products_id, $quantity, $releation_table)) {
                    $this->insert([
                        'guest_serial' => $serialData['serial'],
                        'products_id' => $products_id,
                        'quantity' => $quantity,
                        'expire' => $serialData['expire'],
                    ]);
                } else {
                    return (new Error('購買數量超過庫存數量', []))->__toArray();
                }
            } else {
                $row = $resultSet->current();
                $quantity = intval($row->quantity);
                $quantity += 1;
                if ($this->stockVerify($products_id, $quantity, $releation_table)) {
                    $this->update([
                        'quantity' => $quantity,
                        'expire' =>  $serialData['expire'],
                    ], $where);
                } else {
                    return (new Error('購買數量超過庫存數量', []))->__toArray();
                }
            }
        } catch (\Exception $e) {
            TryCatchTransToLog($e);
            return (new Error('加入購物車失敗', []))->__toArray();
        }

        return (new Success('加入購物車成功', []))->__toArray();
    }

    /**
     *
     * @return \Laminas\Db\Adapter\Driver\ResultInterface
     */
    public function removeExpired()
    {
        $delete = new Delete($this);
        $where = new Where();
        $nowTime = strtotime('now');
        $where->lessThan('expire', $nowTime);
        return $delete->where($where)->excute();
    }

    /**
     *
     * @return array
     */
    public function getGuestSerial()
    {
        $expire_time = strtotime('now') + (86400*7);
        if (empty($_COOKIE['cart_guest_serial'])) {
            $guest_serial = crc32(uniqid($this->table, true).microtime(true));
            $data = [
                'serial' => $guest_serial,
                'expire' => $expire_time,
            ];
            setcookie('cart_guest_serial', json_encode($data), $expire_time);
            return $guest_serial;
        } else {
            $data = json_decode($_COOKIE['cart_guest_serial'], true);
            $guest_serial = $data['serial'];
            $data['expire'] = $expire_time;
            setcookie('cart_guest_serial', json_encode($data), $expire_time);
        }
        return json_decode($_COOKIE['cart_guest_serial'], true);
    }
}
