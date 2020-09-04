<?php


namespace M74asoud\Paymenter\Services\Payment\Contract;


use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Wallet;

trait PaymenterAble {

    private function prepareWalletService(): Wallet {
        $walletService = new Wallet();
        $walletService->init( $this );

        return $walletService;
    }

    public function balance(): Money {
        return $this->prepareWalletService()->balance();
    }

    public function hasMoney( Money $money ) {
        return $this->prepareWalletService()->hasMoney( $money );
    }


}
