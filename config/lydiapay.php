<?php

$host = request()->getHost();
$prefix = 'payment-notify';

return [
    'default_driver' => 'paytr', // henüz kullanılmadı
    'default_currency' => 'usd', // henüz kullanılmadı

    'route_configuration' => [
        'prefix' => $prefix,
        'middleware' => ['web']
    ],

    'balance' => [
        'model' => \App\Models\User::class,
        'field' => 'balance'
    ],

    // confirm order

    'drivers' => [
        'paytr' => [
            'merchant_id' => '', // todo: env al
            'merchant_key' => '',
            'merchant_salt' => '',
            'merchant_ok_url' => "https://{$host}?success=1",
            'merchant_fail_url' => "https://{$host}?fail=1", // fail post geliyor
            'iframe_pay_url' => 'https://www.paytr.com/odeme/api/get-token',
        ],
        'paytr_direct' => [
            'merchant_id' => '',
            'merchant_key' => '',
            'merchant_salt' => '',
            'merchant_ok_url' => "https://{$host}?success=1",
            'merchant_fail_url' => "https://{$host}?fail=1", // fail post geliyor
            'direct_pay_url' => 'https://www.paytr.com/odeme',
            'status_3d' => false // true or false
        ],

        'ziraat' => [
            'clientid' => '190400000',
            'storekey' => '123456', // İş yeri anahtarı
            'storetype' => 'pay_hosting',
            'ok_url' => "https://{$host}/{$prefix}/ziraat",
            'fail_url' => "https://{$host}/{$prefix}/ziraat?fail=1",
            'pay_url' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
        ],
        'ziraat_3d' => [
            'clientid' => '190400000',
            'storekey' => '123456', // İş yeri anahtarı
            'storetype' => '3d_pay_hosting',
            'ok_url' => "https://{$host}/{$prefix}/ziraat",
            'fail_url' => "https://{$host}/{$prefix}/ziraat?fail=1",
            'pay_url' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
        ]
    ],

    'map' => [
        'paytr' => \DataGrade\LydiaPay\Drivers\Paytr::class,
        'paytr_direct' => \DataGrade\LydiaPay\Drivers\PaytrDirect::class,
        'ziraat' => \DataGrade\LydiaPay\Drivers\Ziraat::class,
    ]
];

