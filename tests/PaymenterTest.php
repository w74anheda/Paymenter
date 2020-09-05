<?php

namespace Tests\Unit;

use M74asoud\Paymenter\ObjectValue\Money as ObjectValueMoney;
use M74asoud\Paymenter\Services\Payment\Paymenter;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Types\Wallet;
use M74asoud\Paymenter\Services\Payment\Validations\Contract\AbstractPaymenterValidation;
use M74asoud\Paymenter\Services\Payment\Validations\Exceptions\InvalidPaymenterType;
use M74asoud\Paymenter\Services\Payment\Validations\Exceptions\MoneyOutOfRange;
use M74asoud\Paymenter\Services\Payment\Validations\Handler\Money;
use M74asoud\Paymenter\Services\Payment\Validations\Handler\PaymenterType;
use M74asoud\Paymenter\Services\UserInstance;
use Illuminate\Support\Str;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\PaymenterTDO;
use M74asoud\Paymenter\Services\Payment\Types\Online;
use Tests\TestCase;

class PaymenterTest extends TestCase
{
    private $paymenter = Paymenter::class;

    public function test_is_peymenter_class_pay_and_recharge_method_exists()
    {
        $this->assertTrue(
            class_exists($this->paymenter)
        );

        $this->assertTrue(
            method_exists($this->paymenter, 'pay')
        );

        $this->assertTrue(
            method_exists($this->paymenter, 'recharge')
        );
    }

    public function test_paymenter_type_interface_has_method_pay_verify()
    {
        $this->assertTrue(
            method_exists(PaymenterTypeInterface::class, 'apply')
        );
    }

    public function test_pay_and_recharge_type_is_exist_and_implement_paymenter_type_interface()
    {

        foreach (Paymenter::PAY_TYPES as $type) {
            $this->assertTrue(
                class_exists($type)
            );
            $this->assertTrue(new $type instanceof PaymenterTypeInterface);
        }

        foreach (Paymenter::RECHARGE_TYPES as $type) {
            $this->assertTrue(
                class_exists($type)
            );
            $this->assertTrue(new $type instanceof PaymenterTypeInterface);
        }
    }

    public function test_paymenter_money_validation()
    {
        $moneyHandler = new Money;

        try {
            $moneyHandler->handle(
                new ObjectValueMoney(
                    Paymenter::MIN_PAYABLE_MONEY[ObjectValueMoney::CURRENCY_IRR] - 100
                ),
                new Wallet,
                true
            );
            $this->assertTrue(false);
        } catch (MoneyOutOfRange $err) {
            $this->assertTrue(true);
        }

        try {
            $moneyHandler->handle(
                new ObjectValueMoney(
                    Paymenter::MAX_PAYABLE_MONEY[ObjectValueMoney::CURRENCY_IRR] + 100
                ),
                new Wallet,
                true
            );
            $this->assertTrue(false);
        } catch (MoneyOutOfRange $err) {
            $this->assertTrue(true);
        }


        $res = $moneyHandler->handle(
            new ObjectValueMoney(
                Paymenter::MAX_PAYABLE_MONEY[ObjectValueMoney::CURRENCY_IRR]
            ),
            new Wallet,
            true
        );

        $this->assertTrue($res === AbstractPaymenterValidation::PROCESS_FINISHED);
    }
    public function test_paymenter_paymeterType_validation()
    {
        $paymenterTypeHandler = new PaymenterType;

        $res = $paymenterTypeHandler->handle(
            new ObjectValueMoney(1000),
            new Wallet,
            true
        );

        $this->assertTrue($res === AbstractPaymenterValidation::PROCESS_FINISHED);


        try {
            $res = $paymenterTypeHandler->handle(
                new ObjectValueMoney(1000),
                new Wallet,
                false
            );
            $this->assertTrue(false);
        } catch (InvalidPaymenterType $err) {
            $this->assertTrue(true);
        }
    }

    public function test_paymenter_pay_method()
    {
        $user = factory(UserInstance::UserModelClass())->create(['hash' => Str::uuid()]);
        $paymenterService = new Paymenter;

        $bill = $paymenterService->pay(
            new Online,
            $user->hash,
            new PaymenterTDO(
                new ObjectValueMoney(250000),
                'test for payment by laravel paymenter package',
                100
            )
        );

        if ($bill->status === Bill::Status['watingPay']) {
            $this->assertTrue(
                is_string($bill->paymentTransaction->request_link)
            );
        } else {
            $this->assertTrue(
                $bill->paymentTransaction->status === PaymentTransaction::STATUS['failed']
            );
        }

        // dd($bill->paymentTransaction->request_link);
        // dd($bill->paymentTransaction->request_link);
        // dd($bill->toArray());
    }
    public function test_paymenter_recharge_method()
    {
        $user = factory(UserInstance::UserModelClass())->create(['hash' => Str::uuid()]);
        $paymenterService = new Paymenter;

        $bill = $paymenterService->recharge(
            new Online,
            $user->hash,
            new PaymenterTDO(
                new ObjectValueMoney(100000000),
                'test for recharge wallet by laravel paymenter package',
                100
            )
        );


        if ($bill->status === Bill::Status['watingPay']) {
            $this->assertTrue(
                is_string($bill->paymentTransaction->request_link)
            );
        } else {
            $this->assertTrue(
                $bill->paymentTransaction->status === PaymentTransaction::STATUS['failed']
            );
        }

        // dd('recharge test', $bill->paymentTransaction->request_link);

        // dd($bill->paymentTransaction->request_link);
        // dd($bill->paymentTransaction->request_link);
        // dd($bill->toArray());
    }
}
