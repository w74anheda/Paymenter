<?php


namespace M74asoud\Paymenter\Services\Payment\Types\Contract;


use M74asoud\Paymenter\Models\Bill;

interface PaymenterTypeInterface {

    public function apply( Bill $bill): Bill;

}
