<?php

return [
    'default' => 'FedEx',

    'FedEx' => [
        'key' => env('FEDEX_API_KEY'),
        'password' => env('FEDEX_API_PASSWORD'),
        'account' => env('FEDEX_API_ACCOUNT'),
        'meter' => env('FEDEX_API_METER'),
        'debug' => env('FEDEX_DEBUG'),

        'version' => [
            'address' => [
                'ServiceId' => 'aval',
                'Major' => '4',
                'Intermediate' => '0',
                'Minor' => '0'
            ],
        ],
    ],
];
