<?php

namespace Chopin\Store\CouponRule;

class FreeShippingCouponRule extends AbstractRule
{

     /*
     *
     * @param number $subtotal
     * @param number $target_value
     * @return number
     */
    public function getValue($subtotal, $target_value)
    {
        if ($subtotal >= $target_value) {
            return 0;
        }
        return PHP_INT_MAX;
    }
}
