<?php

namespace M74asoud\Paymenter\Services\Payment;

use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Card2Card;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Types\Online;
use M74asoud\Paymenter\Services\Payment\Types\Wallet;
use M74asoud\Paymenter\Services\Payment\Validations\Contract\AbstractPaymenterValidation;
use Illuminate\Support\Str;

class Paymenter
{

    const MIN_PAYABLE_MONEY = [
        Money::CURRENCY_IRR  => 10000
    ];

    const MAX_PAYABLE_MONEY = [
        Money::CURRENCY_IRR => 1000000000
    ];

    const PAY_TYPES = [
        'wallet'    => Wallet::class,
        'online'    => Online::class,
        // 'card2card' => Card2Card::class
    ];

    const RECHARGE_TYPES = [
        'online'    => Online::class,
        'card2card' => Card2Card::class
    ];

    public function pay(PaymenterTypeInterface $paymenter_type, string $user_hash , PaymenterTDO $paymenterTDO): Bill
    {
        $bill = Bill::create([
            'user_hash' => $user_hash,
            'hash'      => Str::uuid(),
            'status'    => Bill::Status['pending'],
            'amount'    => $paymenterTDO->getAmount()->getValue(),
            'actionType' => Bill::ActionType['payment'],
            'description' => $paymenterTDO->getDescription(),
            'type'      => $paymenterTDO->getType(),
        ]);

        $validationResponse = AbstractPaymenterValidation::start(
            $paymenterTDO->getAmount(),
            $paymenter_type,
            true
        );

        if ($validationResponse !== AbstractPaymenterValidation::PROCESS_FINISHED) {
            $bill->setError();
            return $bill;
        }

        return $paymenter_type->apply($bill);
    }

    public function recharge(PaymenterTypeInterface $paymenter_type, string $user_hash ,  PaymenterTDO $paymenterTDO): Bill
    {
        $bill = Bill::create([
            'user_hash' => $user_hash,
            'hash'      => Str::uuid(),
            'status'    => Bill::Status['pending'],
            'amount'    => $paymenterTDO->getAmount()->getValue(),
            'actionType' => Bill::ActionType['recharge'],
            'description' => $paymenterTDO->getDescription(),
            'type'      => $paymenterTDO->getType(),
        ]);

        $validationResponse = AbstractPaymenterValidation::start(
            $paymenterTDO->getAmount(),
            $paymenter_type,
            false
        );

        if ($validationResponse !== AbstractPaymenterValidation::PROCESS_FINISHED) {
            $bill->setError();
            return $bill;
        }

        return $paymenter_type->apply($bill);

    }
}
