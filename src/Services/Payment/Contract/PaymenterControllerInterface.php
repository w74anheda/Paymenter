<?php

namespace M74asoud\Paymenter\Services\Payment\Contract;

use M74asoud\Paymenter\Models\Bill;

interface PaymenterControllerInterface
{

    public function verifyHandler(Bill $bill);

}
