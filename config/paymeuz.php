<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payme Merchant ID
    |--------------------------------------------------------------------------
    |
    | Your merchant ID from Payme cabinet
    |
    */
    'merchant_id' => env('URFYSOFT_PAYME_MERCHANT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Payme Secret Key
    |--------------------------------------------------------------------------
    |
    | Your secret key from Payme cabinet
    |
    */
    'secret_key' => env('URFYSOFT_PAYME_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Payme API Base URL
    |--------------------------------------------------------------------------
    |
    | Base URL for Payme API
    | Production: https://checkout.paycom.uz/api
    | Test: https://checkout.test.paycom.uz/api
    |
    */
    'base_url' => env('URFYSOFT_PAYME_BASE_URL', 'https://checkout.test.paycom.uz/api'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout for HTTP requests in seconds
    |
    */
    'timeout' => env('URFYSOFT_PAYME_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Currency code (860 for UZS)
    |
    */
    'currency' => env('URFYSOFT_PAYME_CURRENCY', 860),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable/disable request and response logging
    |
    */
    'logging' => [
        'enabled' => env('URFYSOFT_PAYME_LOGGING_ENABLED', true),
        'channel' => env('URFYSOFT_PAYME_LOGGING_CHANNEL', 'stack'),
    ],
];
