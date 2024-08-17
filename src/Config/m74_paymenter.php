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
            ],
            'parsian' => [
                'apiPurchaseUrl' => env('PARSIAN_PURCHASE_URL', 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl'),
                'apiPaymentUrl' => env('PARSIAN_PAYMENT_URL', 'https://pec.shaparak.ir/NewIPG'),
                'apiVerificationUrl' => env('PARSIAN_VERIFY_URL', 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?wsdl'),
                'merchantId' => env('PARSIAN_MERCHANT_ID', '12345678'),
                'pinCode' => env('PARSIAN_PIN_CODE', 'XXXXXXXXXXXXXXXXXXXX')
            ],
        ],

    ];
