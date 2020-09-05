<?php


namespace M74asoud\Paymenter\Services\Payment\Contract;

use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Paymenter;
use M74asoud\Paymenter\Services\Payment\PaymenterTDO;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Types\Online;
use M74asoud\Paymenter\Services\Payment\Types\Wallet;
use M74asoud\Paymenter\Services\UserInstance;

trait PaymenterAble
{

    public function balance(): Money
    {
        return $this->prepareWalletService()->balance();
    }

    public function hasMoney(Money $money)
    {
        return $this->prepareWalletService()->hasMoney($money);
    }

    public function pay(PaymenterTDO $paymenterTDO, PaymenterTypeInterface $paymenter_type = null): Bill
    {
        if (is_null($paymenter_type)) {
            $paymenter_type = new Wallet;
        }
        return $this->preparePaymentService()->pay(
            $paymenter_type,
            $this->{UserInstance::UserHashField()},
            $paymenterTDO
        );
    }

    public function recharge(PaymenterTDO $paymenterTDO, PaymenterTypeInterface $paymenter_type = null): Bill
    {
        if (is_null($paymenter_type)) {
            $paymenter_type = new Online();
        }

        return $this->preparePaymentService()->recharge(
            $paymenter_type,
            $this->{UserInstance::UserHashField()},
            $paymenterTDO
        );
    }

    public static function getPaymenterTypes(): array
    {
        return Paymenter::PAY_TYPES;
    }
    
    public static function getOnlinePaymentPortals(): array
    {
        return Online::PORTALS;
    }

    // ----------------------------------------------------
    // HELPER FUNCTIONS -----------------------------------
    // ----------------------------------------------------

    private function preparePaymentService(): Paymenter
    {
        return new Paymenter;
    }

    private function prepareWalletService(): Wallet
    {
        $walletService = new Wallet();
        $walletService->init($this);

        return $walletService;
    }
}
