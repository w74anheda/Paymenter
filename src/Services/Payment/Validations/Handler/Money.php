<?php

namespace M74asoud\Paymenter\Services\Payment\Validations\Handler;

use M74asoud\Paymenter\ObjectValue\Money as ObjectValueMoney;
use M74asoud\Paymenter\Services\Payment\Paymenter;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Validations\Contract\AbstractPaymenterValidation;
use M74asoud\Paymenter\Services\Payment\Validations\Exceptions\MoneyOutOfRange;

class Money extends AbstractPaymenterValidation
{


    public function handle(ObjectValueMoney $money, PaymenterTypeInterface $paymenter_type, bool $isPay)
    {

        $allowedMinMoney = new ObjectValueMoney(
            Paymenter::MIN_PAYABLE_MONEY[$money->getCurrency()],
            $money->getCurrency()
        );

        $allowedMaxMoney = new ObjectValueMoney(
            Paymenter::MAX_PAYABLE_MONEY[$money->getCurrency()],
            $money->getCurrency()
        );


        if (
            $money->isFewer($allowedMinMoney) ||
            $money->isMore($allowedMaxMoney)
        ) {
            throw new MoneyOutOfRange();
        }

        return parent::handle($money,  $paymenter_type, $isPay);
    }
}
