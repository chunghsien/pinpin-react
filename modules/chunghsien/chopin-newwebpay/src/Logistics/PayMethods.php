<?php

namespace Chopin\Newwebpay\Logistics;

use Chopin\Store\Logistics\AbstractPayMethods;

class PayMethods extends AbstractPayMethods
{
    protected function setMethods()
    {
        $this->methods = [
            'CREDIT',
            'CREDITAE',
            'UNIONPAY',
            'WEBATM',
            'VACC',
            'CVS',
            'BARCODE',
            'CVSCOM',
            'ALIPAY',
            'P2G',
            'LINEPAY',
        ];
    }

    public $payMethodMapperLogistics = [
        "CREDIT" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "WEBATM" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "VACC" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "CVS" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "BARCODE" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "CVSCOM" => [
            "cvs_pickup_paid",
        ],
        "ALIPAY" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "P2G" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
        "LINEPAY" => [
            "home_delivery",
            "cvs_pickup_not_paid",
        ],
    ];
    
    
    protected function setCvsMethods()
    {
        $this->cvsMethods = [
            'CVS',
            'CVSCOM', // 超商取貨付款
        ];
    }

    /**
     *
     * @return array
     */
    public function getPayMethodOptions($language_id=0, $locale_id=0)
    {
        $options = [];
        $allow_pay_methods = config('third_party_service.logistics.allow_pay_methods');
        
        foreach ($this->payMethodMapperLogistics as $value => $values) {
            
            if ($allow_pay_methods) {
                if (false !== array_search($value, $allow_pay_methods)) {
                    $options [] = [
                        'value' => $value,
                        'mapper' => json_encode($values),
                        'label' => $value,
                    ];
                }
            } else {
                $options [] = [
                    'value' => $value,
                    'mapper' => json_encode($values),
                    'label' => $value,
                ];
            }
        }
        return $options;
    }
    /**
     * * 付款方式可對應的物流方式
     */
    protected function setPayMethodMapperLogistics()
    {
        //third_party_service
        $allows = config('third_party_service.logistics.allow_pay_methods');
        foreach ($this->payMethodMapperLogistics as $method => $logistic) {
            if (false === array_search($method, $allows)) {
                unset($this->payMethodMapperLogistics[$method]);
            }
        }
        foreach ($this->methods as $key => $method) {
            if (false === array_search($method, $allows)) {
                unset($this->methods[$key]);
            }
        }
        $this->methods = array_values($this->methods);
    }
}
