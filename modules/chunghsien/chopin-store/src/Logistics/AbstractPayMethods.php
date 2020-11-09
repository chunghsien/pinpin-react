<?php

namespace Chopin\Store\Logistics;

abstract class AbstractPayMethods implements \ArrayAccess
{
    protected $methods = [];

    protected $cvsMethods = [];

    public $payMethodMapperLogistics;

    const HOME_DELIVERY_KEY = 'home_delivery';
    const CVS_PICKUP_PAID_KEY = 'cvs_pickup_paid';
    const CVS_PICKUP_NOT_PAID_KEY = 'cvs_pickup_not_paid';

    public function __construct()
    {
        $this->setCvsMethods();
        $this->setMethods();
        $this->setPayMethodMapperLogistics();
    }

    abstract protected function setMethods();

    abstract protected function setCvsMethods();

    abstract protected function setPayMethodMapperLogistics();

    abstract public function getPayMethodOptions($language_id=0, $locale_id=0);

    /**
     *
     * @return array|string[]
     */
    public function getCvsMethods()
    {
        return $this->cvsMethods;
    }


    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->methods[] = $value;
        } else {
            $this->methods[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->methods[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->methods[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->methods[$offset]) ? $this->methods[$offset] : null;
    }
}
