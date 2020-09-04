<?php

namespace M74asoud\Paymenter\Services\Payment\Validations\Handler;

use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Paymenter;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Validations\Contract\AbstractPaymenterValidation;
use M74asoud\Paymenter\Services\Payment\Validations\Exceptions\InvalidPaymenterType;

class PaymenterType extends AbstractPaymenterValidation
{


    public function handle(Money $money, PaymenterTypeInterface $paymenter_type, bool $isPay)
    {

        $exception = true;
        
        foreach ($isPay ? Paymenter::PAY_TYPES : Paymenter::RECHARGE_TYPES as $type) {
            if($paymenter_type instanceof $type) $exception = false;
        }

        if ($exception) {
            throw new InvalidPaymenterType;
        }

        return parent::handle($money,  $paymenter_type, $isPay);
    }
}
