<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Models\PaymentTransaction;
use M74asoud\Paymenter\Services\Payment\Contract\PaymenterControllerInterface;
use M74asoud\Paymenter\Services\Payment\Types\Online;
use M74asoud\Paymenter\Services\Payment\Types\Wallet;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('paymenter/request/{resNum}', function (Request $request) {

    $paymentTransaction = PaymentTransaction::where('resNum', $request->resNum)
        ->whereIn('status', [PaymentTransaction::STATUS['pending'], PaymentTransaction::STATUS['waitingVerify']])
        ->firstOrFail();

    $portal = Online::PORTALS[$paymentTransaction->portal];

    if (!isset($portal)) {
        abort(403);
    }

    return $portal::request_link($paymentTransaction);
})->name('paymenter.request.link');



Route::any('paymenter/verify', function (Request $request) {

    $verifyHandler = resolve(PaymenterControllerInterface::class);
    $paymentTransaction = PaymentTransaction::where('resNum', $request->resNum)->firstOrFail();
    $bill = $paymentTransaction->bill;

    if ($bill->status !== Bill::Status['watingPay']) {
        return $verifyHandler->verifyHandler($bill);
    }

    DB::beginTransaction();

    try {

        if (!isset(Online::PORTALS[$request->portal])) {

            throw new Exception('portal invalid');
        }

        $bill = resolve(Online::PORTALS[$request->portal])->verify(
            $request,
            $paymentTransaction
        );

        if ($bill->status === Bill::Status['paid'] && $bill->actionType === Bill::ActionType['recharge']) {
            $walletManager = new Wallet();
            $walletManager->apply($bill);
        }

        DB::commit();
    } catch (Exception $err) {
        DB::rollBack();
        report($err);
        $bill = $bill->setError();
    }

    return $verifyHandler->verifyHandler($bill);
})->name('paymenter.verify');
