<?php

namespace M74asoud\Paymenter\Services\Payment\Types\Portals;

use Exception;
use Illuminate\Http\Request;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Types\Portals\Contract\OnlinePortalInterface;
use Illuminate\Support\Str;
use SoapClient;
use GuzzleHttp\Client;

class Saman implements OnlinePortalInterface
{

    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([]);
    }


    public function request(Bill $bill, array $data = []): PaymentTransaction
    {

        $transaction = PaymentTransaction::create([
            'user_hash' => $bill->user_hash,
            'bill_hash' => $bill->hash,
            'amount' => $bill->amount,
            'resNum' => Str::uuid(),
            'status' => PaymentTransaction::STATUS['waitingVerify'],
            'portal' => $data['portal_key'],
        ]);

        return $transaction;
    }

    public static function request_link(PaymentTransaction $paymentTransaction)
    {

        return view('portals::Saman.requestPay')->with([
            'paymentTransaction' => $paymentTransaction,
            'terminalId' => config('m74_paymenter.portals.saman.terminalId'),
            'requestURL' => config('m74_paymenter.portals.saman.RequestURL'),
            'callBackUrl' => route('paymenter.verify') . "?resNum={$paymentTransaction->resNum}" . "&portal={$paymentTransaction->portal}",
        ]);
    }


    public function verify(Request $request, PaymentTransaction $paymentTransaction): Bill
    {
        $bill = $paymentTransaction->bill;

        dd('saman verify method', $request->all());


        // $MerchantCode = "";

        // if(isset($_POST['State']) && $_POST['State'] == "OK") {

        //     $soapclient = new soapclient('https://verify.sep.ir/Payments/ReferencePayment.asmx?WSDL');
        //     $res 		= $soapclient->VerifyTransaction($_POST['RefNum'], $MerchantCode);

        //     if( $res <= 0 )
        //     {
        //         // Transaction Failed
        //         echo "Transaction Failed";
        //     } else {
        //         // Transaction Successful
        //         echo "Transaction Successful";
        //         echo "Ref : {$_POST['RefNum']}<br />";
        //         echo "Res : {$res}<br />";
        //     }
        // } else {
        //     // Transaction Failed
        //     echo "Transaction Failed";
        // }

        return $bill;
    }
}
