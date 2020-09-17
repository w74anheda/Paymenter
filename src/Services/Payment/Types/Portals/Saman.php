<?php

namespace M74asoud\Paymenter\Services\Payment\Types\Portals;

use Exception;
use Illuminate\Http\Request;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Contract\OnlinePortalInterface;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use SoapClient;

class Saman implements OnlinePortalInterface
{

    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

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
            $response = $this->httpClient->post(
                config('m74_paymenter.portals.saman.tokenUrl'),
                [
                    'form_params' => [
                        'Action' => 'token',
                        'TerminalId' =>  config('m74_paymenter.portals.saman.terminalId'),
                        'RedirectURL' => route('paymenter.verify') . "?resNum={$transaction->resNum}" . "&portal=saman",
                        'ResNum' => $transaction->resNum->toString(),
                        'Amount' => $transaction->amount,
                    ]
                ]
            );
            $responseData = json_decode($response->getBody(), true);

            if (
                $response->getStatusCode() !== 200 ||
                !isset($responseData['status']) ||
                $responseData['status'] !== 1 ||
                !isset($responseData['token'])
            ) {
                throw new Exception(json_encode($responseData));
            }

            $transaction->setWaitingVerify(['request_link' => $responseData['token']]);

            DB::commit();
        } catch (Exception $err) {
            report($err);
            DB::rollBack();
            $transaction->setFaild(['error' => $err->getMessage()]);
            // dd(json_decode($err->getMessage(), true));
        }

        return $transaction;
    }

    public static function request_link(PaymentTransaction $paymentTransaction)
    {

        return view('portals::Saman.requestPay')->with([
            'token' => $paymentTransaction->request_link,
            'formRequestUrl' => config('m74_paymenter.portals.saman.formRequestUrl'),
        ]);
    }

    public function verify(Request $request, PaymentTransaction $transaction): Bill
    {
        //bank-request => ‫‪ResNum‬‬ - ‫‪RefNum‬‬ - ‫‪TraceNo‬‬ - ‫‪State‬‬ - ‫‪Status‬‬ - ‫‪TerminalId‬‬
        $bill = $transaction->bill;
        $fakeRefNum = !!PaymentTransaction::where('refNum', $request->‫‪RefNum)->first();

        if (
            $transaction->status !==
            PaymentTransaction::STATUS['waitingVerify']
        ) {
            return $bill;
        }

        DB::beginTransaction();

        try {

            if (
                $fakeRefNum ||
                $transaction->resNum !== $request->ResNum ||
                $request->State !== 'OK' ||
                $request->‫‪TerminalId‬‬ !==  config('m74_paymenter.portals.saman.terminalId')

            ) {
                throw new Exception(json_encode($request));
            }

            $soapclient = new SoapClient(config('m74_paymenter.portals.saman.soapService'));
            $amount         = $soapclient->VerifyTransaction(
                $request->‫‪RefNum‬‬,
                config('m74_paymenter.portals.saman.terminalId')
            );

            if ($amount !== $transaction->amount) {
                throw new Exception(json_encode($request));
            }

            $transaction->setPaid([
                'refNum' => $request->‫‪RefNum‬‬,
                'additional' => json_encode($request)
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
