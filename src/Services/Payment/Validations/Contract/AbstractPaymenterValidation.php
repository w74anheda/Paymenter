<?php

namespace M74asoud\Paymenter\Services\Payment\Validations\Contract;

use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Validations\Handler\Money as HandlerMoney;
use M74asoud\Paymenter\Services\Payment\Validations\Handler\PaymenterType;

abstract class AbstractPaymenterValidation implements PaymenterValidationInterface
{

    private $nextHandler;
    const PROCESS_FINISHED = 'process.finished';

    public function setNext(PaymenterValidationInterface $handler): PaymenterValidationInterface
    {
        $this->nextHandler = $handler;

        return $handler;
    }

    public function handle(Money $money, PaymenterTypeInterface $paymenter_type, bool $isPay)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($money,  $paymenter_type, $isPay);
        }

        return self::PROCESS_FINISHED;
    }

    public static function start(Money $money, PaymenterTypeInterface $paymenter_type, bool $isPay)
    {
        $moneyHandler = new HandlerMoney;
        $paymenterTypeHandler = new PaymenterType;

        $moneyHandler->setNext($paymenterTypeHandler);

        return $moneyHandler->handle($money,  $paymenter_type,  $isPay);
    }
    
}
