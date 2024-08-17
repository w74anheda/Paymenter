<?php


namespace M74asoud\Paymenter\Services\Payment\Types;

use Exception;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Saman;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Parsian;
use M74asoud\Paymenter\Services\Payment\Types\Portals\ZarinPal;

class Online implements PaymenterTypeInterface
{
    private $portal;

    const PORTALS = [
        'ZARINPAL' => ZarinPal::class,
        'SAMAN' => Saman::class,
        'PARSIAN' => Parsian::class
    ];

    public function __construct(string $portal = 'ZARINPAL')
    {
        $this->portal = $portal;

        if (!isset(self::PORTALS[$this->portal])) {
            throw new Exception('online bank portal invalid key');
        }
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
