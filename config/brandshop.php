<?php
return [
    'braintree' => [
        'environment' => env('BRAINTREE_ENVIRONMENT', 'production'),
        'merchantId' => env('BRAINTREE_MERCHANT_ID'),
        'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
        'privateKey' => env('BRAINTREE_PRIVATE_KEY'),
    ],
    'agent' => [
        'api' => env('AGENT_API_URL'),
    ],
];