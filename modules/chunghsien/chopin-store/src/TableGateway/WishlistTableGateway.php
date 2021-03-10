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

class WishlistTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = true;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'wishlist';

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
    
    public function addWishlist(ServerRequestInterface $request)
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
        if($this->select($where)->count() == 0)
        {
            $this->insert([
                "guest_serial" => $params['guest_serial'],
                "products_id" => $params['products_id'],
                "expire" => 0
            ]);
            $this->update(["expire" => strtotime("today") + (86400 * 7)], ["guest_serial" => $serial]);
        }
        $wishlists = $this->getWishlist($request);
        return array_merge([
            'status' => 'success',
            'message' => [""],
        ], $wishlists);
        
        
    }
    public function getWishlist(ServerRequestInterface $request)
    {
        $guest_serial = $request->getAttribute('method_or_id', null);
        if(!$guest_serial) {
            $guest_serial = $this->getGuestSerial($request);
        }
        $this->removeExpired($guest_serial);
        $resultSet = $this->select([
            'guest_serial' => $guest_serial
        ]);
        
        $productsTableGateway = new ProductsTableGateway($this->adapter);
        $result = [];
        $products_ids = [];
        foreach ($resultSet as $wishlistRow) {
            $products_ids[] = $wishlistRow->products_id;
        }
        $productsResultSet = $productsTableGateway->select(['id' => $products_ids]);
        foreach ($productsResultSet as $productsRowGateway)
        {
            /**
             * @var ProductsRowGateway $productsRowGateway
             */
            $productsRowGateway->withAssets();
            $productsRowGateway->withSpec();
            $productsRowGateway->withSpecGroup();
            $productsRowGateway->withItemSumStock($productsRowGateway->id);
            $result[] = $productsRowGateway->toArray();
        }
        return [
            "wishlists" => $result,
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
