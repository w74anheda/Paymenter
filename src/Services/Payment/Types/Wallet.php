<?php

namespace M74asoud\Paymenter\Services\Payment\Types;

use Exception;
use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\Wallet as ModelsWallet;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\UserInstance;

class Wallet implements PaymenterTypeInterface
{

    private $user;
    private $wallet;

    public function init($user)
    {
        UserInstance::isInstanceOfUser($user);
        $this->user = $user;
        $this->wallet = $this->getUserWallet();
    }

    public function apply(Bill $bill): Bill
    {
       
        $this->user = $bill->user;
        $this->wallet = $this->getUserWallet();

        $aplliedBill = $bill;

        switch ($bill->actionType) {

            case Bill::ActionType['payment']:
                $aplliedBill = $this->payHandler($bill);
                break;

            case Bill::ActionType['recharge']:
                $aplliedBill = $this->rechargeHandler($bill);
                break;
        }

        return $aplliedBill;
    }

    public function balance(): Money
    {
        $wallet = $this->wallet->last();

        return $wallet instanceof ModelsWallet
            ? new Money($this->wallet->last()->balance)
            : new Money(0);
    }

    public function hasMoney(Money $money): bool
    {
        return $this->balance()->isMoreEqual($money);
    }

    private function getUserWallet(): Collection
    {
        return \M74asoud\Paymenter\Models\Wallet::where(
            'user_hash',
            $this->user->{UserInstance::UserHashField()}
        )->get();
    }


    private function exchange(Money $amount, bool $isPay = true): ModelsWallet
    {
        if ($isPay && !$this->hasMoney($amount)) {
            throw new InvalidPayloadException('Not Enough Money');
        }

        $balance = $this->balance();

        $wallet = ModelsWallet::create([
            'user_hash' => $this->user->{UserInstance::UserHashField()},
            'before' => $balance->getValue(),
            'amount' => $isPay ? -$amount->getValue() : +$amount->getValue(),
            'balance' => $isPay ? $balance->reduce($amount)->getValue() : $balance->add($amount)->getValue()
        ]);

        return $wallet;
    }

    private function payHandler(Bill $bill): Bill
    {
        $billAmount = new Money($bill->amount);
        $aplliedBill = $bill;

        DB::beginTransaction();
        try {
            if (!$this->hasMoney($billAmount)) {
                $aplliedBill = $bill->setNoEnoghMoney();
            } else {
                $wallet = $this->exchange($billAmount, true);

                $aplliedBill = $bill->setPaid($wallet->id, ModelsWallet::class);
            }
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $aplliedBill = $bill->setError();
        }

        return $aplliedBill;
    }

    private function rechargeHandler(Bill $bill): Bill
    {
        $billAmount = new Money($bill->amount);
        $aplliedBill = $bill;

        DB::beginTransaction();
        try {

            $wallet = $this->exchange($billAmount, false);
            $aplliedBill = $bill->setWallet($wallet);

            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $aplliedBill = $bill->setError();
        }


        return $aplliedBill;
    }
}
