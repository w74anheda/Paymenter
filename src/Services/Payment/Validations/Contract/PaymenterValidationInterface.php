<?php

namespace M74asoud\Paymenter\Services\Payment\Validations\Contract;

use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;

interface PaymenterValidationInterface
{
    public function setNext(PaymenterValidationInterface $handler): PaymenterValidationInterface;

    public function handle(Money $money, PaymenterTypeInterface $paymenter_type, bool $isPay);
}
