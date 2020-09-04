<?php


namespace M74asoud\Paymenter\Services\Payment\Types;


use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;

class Card2Card implements PaymenterTypeInterface {

    public function apply( Bill $bill): Bill {
        // TODO: Implement apply() method.
    }
}
