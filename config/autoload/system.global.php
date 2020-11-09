<?php

use Chopin\Newwebpay\Service\NewwebpayService;

return [
    'system_settings' => [
        'imageOptimizer' => true,
        //cdn server upload ，還沒想到怎樣做
        'uploadPosition' => 'webServer'
    ],
    'third_party_service' => [
        'logistics' => [
            'manufacturer' => 'newwebpay',
            'service_class' => NewwebpayService::class,
            'allow_pay_methods' => [
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
            ],
        ]
        
    ],
];