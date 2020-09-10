<?php

namespace M74asoud\Paymenter\Services\Payment\Types\Portals;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Contract\OnlinePortalInterface;
use Illuminate\Support\Str;
use SoapClient;

class ZarinPal implements OnlinePortalInterface
{
    public function request(Bill $bill, array $data = []): PaymentTransaction
    {

        $transaction = PaymentTransaction::create([
            'user_hash' => $bill->user_hash,
            'bill_hash' => $bill->hash,
            'amount' => $bill->amount,
            'resNum' => Str::uuid(),
            'status' => PaymentTransaction::STATUS['pending'],
            'portal' => $data['portal_key'],
        ]);

        DB::beginTransaction();

        try {

            $client = new SoapClient(
                config('m74_paymenter.portals.zarinpal.RequestClientURL'),
                [
                    'encoding' => 'UTF-8',
                    'cache_wsdl'     => WSDL_CACHE_NONE,
                    'trace'          => 1,
                    'stream_context' => stream_context_create(
                        [
                            'ssl' => [
                                'verify_peer'       => false,
                                'verify_peer_name'  => false,
                                'allow_self_signed' => true
                            ]
                        ]
                    )
                ]
            );

            $result = $client->PaymentRequest(
                [
                    'MerchantID' => config('m74_paymenter.portals.zarinpal.merchant'),
                    'Amount' => $transaction->amount,
                    'Description' => $bill->description,
                    'CallbackURL' => route('paymenter.verify') . "?resNum={$transaction->resNum}" . "&portal={$data['portal_key']}",
                ]
            );
            if ($result->Status !== 100) {

                throw new Exception('status not OK');
            }

            $transaction->update([
                'status' => PaymentTransaction::STATUS['waitingVerify'],
                'request_link' => config('m74_paymenter.portals.zarinpal.RequestURL') . '/' . $result->Authority
            ]);

            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            report($err);

            $transaction->update([
                'status' => PaymentTransaction::STATUS['failed'],
            ]);
        }

        return $transaction;
    }

    public static function request_link(PaymentTransaction $paymentTransaction)
    {
        return redirect(
            $paymentTransaction->request_link
        );
    }

    public function verify(Request $request, PaymentTransaction $paymentTransaction): Bill
    {
        $bill = $paymentTransaction->bill;

        DB::beginTransaction();

        try {

            if ($request->Status !== 'OK') {

                $paymentTransaction->update(
                    [
                        'status' => PaymentTransaction::STATUS['cancel'],
                        'additional' => $request->all()
                    ]
                );

                $bill->setError();
            } else {

                $client = new SoapClient(
                    config('m74_paymenter.portals.zarinpal.VerifyURL'),
                    [
                        'encoding' => 'UTF-8',
                        'cache_wsdl'     => WSDL_CACHE_NONE,
                        'trace'          => 1,
                        'stream_context' => stream_context_create(
                            [
                                'ssl' => [
                                    'verify_peer'       => false,
                                    'verify_peer_name'  => false,
                                    'allow_self_signed' => true
                                ]
                            ]
                        )
                    ]
                );

                $result = $client->PaymentVerification(
                    [
                        'MerchantID' =>  config('m74_paymenter.portals.zarinpal.merchant'),
                        'Authority' => $request->Authority,
                        'Amount' => $paymentTransaction->amount,
                    ]
                );

                if ($result->Status !== 100) {

                    $paymentTransaction->update(
                        [
                            'status' => PaymentTransaction::STATUS['failed'],
                            'additional' => json_encode($result)
                        ]
                    );

                    $bill->setError();
                } else {

                    $paymentTransaction->update([
                        'status' => PaymentTransaction::STATUS['paid'],
                        'refNum' => $result->RefID,
                        'additional' => json_encode($result)
                    ]);
                    $bill->setPaid($paymentTransaction->id, PaymentTransaction::class);
                }
            }

            DB::commit();
        } catch (Exception $err) {

            DB::rollBack();
            report($err);
            $bill->setError();
        }

        return $bill;
    }
}
