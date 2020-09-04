<?php

namespace M74asoud\Paymenter\Services\Payment\Types\Portals\Contract;

use Illuminate\Http\Request;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\ObjectValue\Money;

interface OnlinePortalInterface
{
    public function request(Bill $bill, array $data = []):PaymentTransaction; // return pay link

    public function verify(Request $request ,PaymentTransaction $paymentTransaction):Bill;
}
