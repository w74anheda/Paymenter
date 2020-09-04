<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\Wallet;
use M74asoud\Paymenter\Services\UserInstance;
use Tests\TestCase;

class BillTest extends TestCase
{

    //use RefreshDatabase;

    public function test_get_bill_user()
    {
        $userClass = UserInstance::UserModelClass();

        $user = factory(UserInstance::UserModelClass())->create(['hash' => Str::uuid()]);
        $bill = factory(Bill::class)->create(['user_hash' => $user->hash]);

        $this->assertNull($bill->paymenterable);
        $this->assertTrue($bill->user instanceof $userClass);
    }

    public function test_bill_create_wallet()
    {
        $user      = factory(UserInstance::UserModelClass())->create(['hash' => Str::uuid()]);
        $bill      = factory(Bill::class)->create(['user_hash' => $user->hash]);
        $wallet    = factory(Wallet::class)->create(
            [
                'user_hash' => $bill->user->hash,
                'amount'    => $bill->amount,
                'balance'   => $bill->amount
            ]
        );

        $attach = $bill->paymenterable()->associate($wallet)->save();

        $this->assertTrue($attach);
        $this->assertTrue($bill->user->id === $wallet->user->id);
        $this->assertTrue($wallet->id === $bill->paymenterable->id);
    }


    public function test_bill_wallet_relation()
    {
        $user      = factory(UserInstance::UserModelClass())->create(['hash' => Str::uuid()]);
        $bill      = factory(Bill::class)->create(['user_hash' => $user->hash, 'actionType' => Bill::ActionType['recharge']]);

        $this->assertNull($bill->wallet);

        $wallet    = factory(Wallet::class)->create(
            [
                'user_hash' => $bill->user->hash,
                'before'    => 0,
                'amount'    => $bill->amount,
                'balance'   => $bill->amount
            ]
        );
        $this->assertNull($wallet->billRecharge);

        $bill->update(['wallet_id' => $wallet->id]);

        $this->assertTrue($bill->wallet()->first()->id === $wallet->id);

        $this->assertTrue($wallet->billRecharge()->first() instanceof Bill);
    }
}
