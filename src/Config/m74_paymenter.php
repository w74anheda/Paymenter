<?php

use App\User;

return
    [

        'user_model' => User::class,
        'tbl_prefix' => 'M74',
        'users_hash_filed_name' => 'hash',
        'users_tbl_name' => 'users',

        'portals' => [
            'zarinpal' => [
                'merchant' => env('ZARINPAL_MERCHANT', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'),
                'RequestClientURL' => env('ZARINPAL_CLIENT_URL', 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl'),
                'RequestURL' => env('ZARINPAL_REQUEST_URL', 'https://sandbox.zarinpal.com/pg/StartPay'),
                'VerifyURL' => env('ZARINPAL_VERIFY_URL', 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl'),
            ],
        ],

    ];
