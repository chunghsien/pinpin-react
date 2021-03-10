<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Select;
use Chopin\Store\RowGateway\ProductsRowGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Where;

class CartTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'cart';

    /**
     *
     * @return number
     */
    public function removeExpired($guest_serial = null)
    {
        $delete = $this->getSql()->delete();
        $predicate = $delete->where;
        $predicate->lessThan('expire', strtotime('now'));
        if ($guest_serial) {
            $predicate->equalTo('guest_serial', $guest_serial);
        }
        $delete->where($predicate);
        return $this->deleteWith($delete);
    }
    
    public function addCart(ServerRequestInterface $request)
    {
        $guest_serial = $this->getGuestSerial($request);
        $serial = $guest_serial['serial'];
        $request = $request->withAttribute('method_or_id', $serial);
        $params = $request->getParsedBody();
        if(!$params) {
            $params = json_decode($request->getBody()->getContents(), true);
        }
        $params = $params['params'];
        $params['guest_serial'] = $serial;
        $where = $this->getSql()->select()->where;
        $where->equalTo('guest_serial', $params['guest_serial']);
        $where->equalTo('products_id', $params['products_id']);
        $where->equalTo('products_spec_group_id', $params['products_spec_group_id']);
        $where->equalTo('products_spec_id', $params['products_spec_id']);
        $row = $this->select($where)->current();
        $quantity = intval($params['quantity']);
        if($row) {
            $quantity = intval($row->quantity) + intval($params['quantity']);
            $row->quantity = $quantity;
        }else {
            $row = new \Chopin\LaminasDb\RowGateway\RowGateway(
                $this->primary,
                $this->table,
                $this->adapter
            );
            $params['expire'] = strtotime("today") + (86400 * 7);
            $row->exchangeArray($params);
        }
        
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $productsSpecTableGateway = new ProductsSpecTableGateway($this->adapter);
        $stock = 0;
        if($params['products_spec_id'] == 0) {
            $productsRow = $productsTableGateway->select(['id' => $params['products_id']])->current();
            $stock = $productsRow->stock;
        }else {
            $productsSpecRow = $productsSpecTableGateway->select(['id' => $params['products_spec_id']])->current();
            $stock = $productsSpecRow->stock;
        }
        $carts = [];
        if($stock < $quantity) {
            ApiErrorResponse::$status = 200;
            $carts = $this->getCart($request);
            return array_merge([
                'status' => 'fail',
                'message' => ["Inventory shortage"],
            ], $carts);
        }
        if($row instanceof RowGatewayInterface) {
            $row->save();
        }else {
            $update = $this->getSql()->update();
            $where = $update->where;
            $where->equalTo('guest_serial', $row->guest_serial);
            $where->equalTo('products_id', $row->products_id);
            $where->equalTo('products_spec_group_id', $row->products_spec_group_id);
            $where->equalTo('products_spec_id', $row->products_spec_id);
            $values = $row;
            $update->set((array)$values)->where($where);
            $this->getSql()->prepareStatementForSqlObject($update)->execute();
        }
        $update = $this->getSql()->update();
        $where = $update->where;
        $where->equalTo('guest_serial', $row->guest_serial);
        $values = ['expire' => (strtotime("today") + (86400 * 7)) ];
        $update->set($values)->where($where);
        $this->getSql()->prepareStatementForSqlObject($update)->execute();
        
        $carts = $this->getCart($request);
        return array_merge([
            'status' => 'success',
            'message' => ["Added To Cart"],
        ], $carts);
        
        
    }
    public function getCart(ServerRequestInterface $request)
    {
        $query = $request->getQueryParams();

        if (isset($query['guest_serial']) && strtolower($request->getMethod()) == 'get') {
            $guest_serial = $query['guest_serial'];
        }
        if (strtolower($request->getMethod()) != 'get') {
            $guest_serial = $request->getAttribute('method_or_id', null);
        }
        if (!$guest_serial) {
            $cart_guest_serial = $this->getGuestSerial();
            $guest_serial = $cart_guest_serial['serial'];
        }
        $this->removeExpired($guest_serial);
        $resultSet = $this->select([
            'guest_serial' => $guest_serial
        ]);
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $result = [];
        $productsDiscountTableGateway = new ProductsDiscountTableGateway($this->adapter);
        foreach ($resultSet as $cartRow) {
            $where = new Where();
            $where->isNull('deleted_at');
            $where->equalTo('id', $cartRow['products_id']);
            $productsResustSet = $productsTableGateway->select($where);
            if($productsResustSet->count() == 1) {
                /**
                 * 
                 * @var ProductsRowGateway $productsRow
                 */
                $productsRow = $productsResustSet->current();
                $productsRow->withAssets();
                $productsRow->withSpec($cartRow['products_spec_id']);
                $productsRow->withSpecGroup($cartRow['products_spec_group_id']);
                $cartRow= array_merge((array)$cartRow, $productsRow->toArray());
                $where = $productsDiscountTableGateway->getSql()->select()->where;
                $now = date("Y-m-d H:i:s");
                $where->isNull('deleted_at');
                $where->lessThanOrEqualTo('start_date', $now);
                $where->greaterThanOrEqualTo('end_date', $now);
                $productsDiscountResultset = $productsDiscountTableGateway->select($where);
                $cartRow['discount'] = 0;
                if($productsDiscountResultset->count() == 1) {
                    $cartRow['discount'] = $productsDiscountResultset->current()->discount;
                }
            }
            $key = $cartRow['products_id'].'-'.$cartRow['products_spec_group_id'].'-'.$cartRow['products_spec_id'];
            $result[$key] = $cartRow;
        }
        return [
            "carts" => $result,
            "guestSerial" => $guest_serial,
        ];
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getGuestSerial(ServerRequestInterface $request)
    {
        $expire_time = strtotime('today') + (86400 * 7);
        $query = $request->getQueryParams();
        if($request->getAttribute('method_or_id', null)) {
            $guest_serial = $request->getAttribute('method_or_id', null);
            return [
                'serial' => $guest_serial,
                'expire' => $expire_time,
            ];
        }
        if (isset($query['guest_serial']) && strtolower($request->getMethod()) == 'get') {
            $guest_serial = $query['guest_serial'];
            return [
                'serial' => $guest_serial,
                'expire' => $expire_time,
            ];
        }
        if (empty($_COOKIE['guest_serial'])) {
            $guest_serial = crc32(uniqid($this->table, true) . microtime(true));
            $data = [
                'serial' => $guest_serial,
                'expire' => $expire_time,
            ];
            setcookie('guest_serial', json_encode($data), $expire_time);
            return $data;
        } else {
            $data = json_decode($_COOKIE['guest_serial'], true);
            $guest_serial = $data['serial'];
            $data['expire'] = $expire_time;
            setcookie('guest_serial', json_encode($data), $expire_time);
        }
        return json_decode($_COOKIE['guest_serial'], true);
    }
}
