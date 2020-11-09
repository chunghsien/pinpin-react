<?php

namespace Chopin\Store\CouponRule;

abstract class AbstractRule
{

    /**
     *
     * @var mixed
     */
    protected $mixed;

    public function __construct($mixed = null)
    {
        if ($mixed) {
            $this->mixed = $mixed;
        }
    }
}
