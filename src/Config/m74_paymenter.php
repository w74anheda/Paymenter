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
            'saman' => [
                'terminalId' => env('SAMAN_TERMINAL_ID', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'),
                'tokenUrl' => env('SAMAN_Token_URL', 'https://sep.shaparak.ir/MobilePG/MobilePayment'),
                'formRequestUrl' => env('SAMAN_FORM_REQUEST_URL', 'https://sep.shaparak.ir/MobilePG/MobilePayment'),
                'soapService' => env('SAMAN_SOAP_SERVICE', 'https://verify.sep.ir/Payments/ReferencePayment.asmx'),
            ]
        ],

    ];
