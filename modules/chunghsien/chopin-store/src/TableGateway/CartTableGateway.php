<?php

namespace Chopin\Store\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

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
    public function removeExpired()
    {
        $delete = $this->getSql()->delete();
        $predicate = $delete->where;
        $predicate->lessThan('expire', strtotime('now'));
        $delete->where($predicate);
        return $this->deleteWith($delete);
    }

    /**
     *
     * @return array
     */
    public function getGuestSerial()
    {
        $expire_time = strtotime('now') + (86400 * 7);
        if (empty($_COOKIE['cart_guest_serial'])) {
            $guest_serial = crc32(uniqid($this->table, true) . microtime(true));
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
