<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\Wallet;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\UserInstance;
use Tests\TestCase;

class WalletTest extends TestCase
{
    //use RefreshDatabase;
    private function createUserAndAttachWallet(): \stdClass
    {
        $hash = Str::uuid();
        $user = factory(UserInstance::UserModelClass())->create(
            [UserInstance::UserHashField() => $hash]
        );

        return (object) [
            'user'    => $user,
            'wallets' => [
                factory(Wallet::class)->create(['user_hash' => $hash]),
                factory(Wallet::class)->create(['user_hash' => $hash]),
                factory(Wallet::class)->create(['user_hash' => $hash])
            ],
        ];
    }

    private function walletService($user)
    {

        $walletService = new \M74asoud\Paymenter\Services\Payment\Types\Wallet();
        $walletService->init($user);

        return $walletService;
    }

    public function test_get_user_balance()
    {

        $data = $this->createUserAndAttachWallet();

        $this->assertTrue($data->user->balance() instanceof Money);
    }

    public function test_user_hasMoney()
    {

        $data = $this->createUserAndAttachWallet();

        $this->assertTrue($data->user->hasMoney(new Money(100)));
        $this->assertFalse($data->user->hasMoney(new Money(1000000)));
    }


    public function test_pay_bill_by_wallet()
    {
        $walletService = new \M74asoud\Paymenter\Services\Payment\Types\Wallet();
        $hash = Str::uuid();
        $user = factory(UserInstance::UserModelClass())->create(
            [UserInstance::UserHashField() => $hash]
        );
        factory(Wallet::class)->create(['user_hash' => $hash]);

        $bill = factory(Bill::class)->create([
            'user_hash' => $user->hash,
            'status' => Bill::Status['pending'],
            'amount' => 100,
            'actionType' => Bill::ActionType['payment']
        ]);


        $pay = $walletService->apply($bill);

        $this->assertTrue($pay instanceof Bill);
        $this->assertTrue($pay->status === Bill::Status['paid']);
        $this->assertTrue($pay->paymenterable_type === Wallet::class);
        $this->assertTrue(is_int($pay->paymenterable_id));



        $bill = factory(Bill::class)->create([
            'user_hash' => $user->hash,
            'status' => Bill::Status['pending'],
            'amount' => 0,
            'actionType' => Bill::ActionType['payment']
        ]);

        $pay = $walletService->apply($bill);

        $this->assertTrue($pay instanceof Bill);
        $this->assertTrue($pay->status === Bill::Status['paid']);
        $this->assertTrue($pay->paymenterable_type === Wallet::class);
        $this->assertTrue(is_int($pay->paymenterable_id));

        $bill = factory(Bill::class)->create([
            'user_hash' => $user->hash,
            'status' => Bill::Status['pending'],
            'amount' => 100000000,
            'actionType' => Bill::ActionType['payment']
        ]);

        $pay = $walletService->apply($bill);

        $this->assertTrue($pay instanceof Bill);
        $this->assertTrue($pay->status === Bill::Status['noEnoughMoney']);
        $this->assertTrue(is_null($pay->paymenterable_type));
        $this->assertTrue(is_null($pay->paymenterable_id));
    }

    public function test_recharge_bill_by_wallet()
    {
        $walletService = new \M74asoud\Paymenter\Services\Payment\Types\Wallet();
        $hash = Str::uuid();
        $user = factory(UserInstance::UserModelClass())->create(
            [UserInstance::UserHashField() => $hash]
        );

        $bill = factory(Bill::class)->create([
            'user_hash' => $user->hash,
            'status' => Bill::Status['pending'],
            'amount' => 3333,
            'actionType' => Bill::ActionType['recharge'],
            'paymenterable_id' => 2,
            'paymenterable_type' => 'App\Test',
        ]);

        $recahrge = $walletService->apply($bill);

        $this->assertTrue($recahrge instanceof Bill);
        $this->assertTrue($recahrge->status !== Bill::Status['pending']);
        $this->assertTrue($recahrge->wallet instanceof Wallet || is_null($recahrge->wallet));
    }
}
