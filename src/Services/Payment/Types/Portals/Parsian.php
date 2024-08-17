<?php

namespace M74asoud\Paymenter\Services\Payment\Types\Portals;

use Exception;
use Illuminate\Http\Request;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Contract\OnlinePortalInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use SoapClient;

class Parsian implements OnlinePortalInterface
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
            $client = new SoapClient(config('m74_paymenter.portals.parsian.apiPurchaseUrl'), [
                'trace' => 1,
                'exceptions' => true,
                'connection_timeout' => 15,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ],
                ])
            ]);
            $params = [
                'requestData' => [
                    'LoginAccount' => config('m74_paymenter.portals.parsian.pinCode'),
                    'OrderId' => $bill->id,
                    'Amount' => $transaction->amount,
                    'CallBackUrl' => route('paymenter.verify').'/'
                ]
            ];

            $response = $client->SalePaymentRequest($params);

            if (
                !isset($response->SalePaymentRequestResult->Status) ||
                $response->SalePaymentRequestResult->Status !== 0 ||
                !isset($response->SalePaymentRequestResult->Token)
            ) {
                throw new Exception(json_encode($response));
            }

            $transaction->setWaitingVerify(['request_link' => $response->SalePaymentRequestResult->Token]);

            DB::commit();
        } catch (Exception $err) {
            report($err);
            DB::rollBack();
            $transaction->setFaild(['error' => $err->getMessage()]);
        }

        return $transaction;
    }

    public static function request_link(PaymentTransaction $paymentTransaction)
    {
        return view('portals::Parsian.requestPay')->with([
            'token' => $paymentTransaction->request_link,
            'formRequestUrl' => config('m74_paymenter.portals.parsian.apiPaymentUrl'),
        ]);
    }

    public function verify(Request $request, PaymentTransaction $transaction): Bill
    {
        $bill = $transaction->bill;

        if ($transaction->status !== PaymentTransaction::STATUS['waitingVerify']) {
            return $bill;
        }

        DB::beginTransaction();

        try {
            if ($request->status != 0 || !$request->Token) {
                throw new Exception(json_encode($request->all()));
            }

            $client = new SoapClient(config('m74_paymenter.portals.parsian.apiVerificationUrl'), [
                'trace' => 1,
                'exceptions' => true,
                'connection_timeout' => 15,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ],
                ])
            ]);

            $params = [
                'requestData' => [
                    'LoginAccount' => config('m74_paymenter.portals.parsian.pinCode'),
                    'Token' => $request->Token
                ]
            ];

            $response = $client->ConfirmPayment($params);

            if (
                !isset($response->ConfirmPaymentResult->Status) ||
                $response->ConfirmPaymentResult->Status !== 0 ||
                !isset($response->ConfirmPaymentResult->RRN) ||
                $response->ConfirmPaymentResult->RRN <= 0
            ) {
                throw new Exception(json_encode($response));
            }

            $transaction->setPaid([
                'refNum' => $response->ConfirmPaymentResult->RRN,
                'additional' => json_encode($response->ConfirmPaymentResult)
            ]);

            $bill->setPaid($transaction->id, PaymentTransaction::class);

            DB::commit();
        } catch (Exception $err) {
            report($err);
            DB::rollBack();
            $transaction->setFaild(['error' => $err->getMessage()]);
            $bill->setError();
        }

        return $bill;
    }
}