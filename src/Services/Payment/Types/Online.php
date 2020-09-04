<?php


namespace M74asoud\Paymenter\Services\Payment\Types;


use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Contract\OnlinePortalInterface;
use M74asoud\Paymenter\Services\Payment\Types\Portals\ZarinPal;

class Online implements PaymenterTypeInterface
{

    const PORTALS = [
        'ZARINPAL' => ZarinPal::class
    ];

    private $default_portal = 'ZARINPAL';
    private $portal;

    public function __construct(OnlinePortalInterface $portal = null)
    {
        $this->portal = $portal ?? $this->default_portal;
    }

    public function apply(Bill $bill): Bill
    {
        $paymentTransaction = resolve(self::PORTALS[$this->portal])
        ->request(
            $bill,
            ['portal_key' => $this->portal]
        );

        $paymentTransaction->status === PaymentTransaction::STATUS['waitingVerify']
        ? $bill->setWatingPay() : $bill->setError();

        return $bill;
    }



}
