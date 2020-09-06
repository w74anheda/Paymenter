## Paymenter 

 - handle user payment 
 - create wallet for user
 - prepare online payment by multi bank portal (by default ZarinPal)
   
  

##  Installation

```bash
$ composer require m74asoud/paymenter
$ php artisan vendor:publish --provider="M74asoud\\Paymenter\\PaymenterServiceProvider"
$ php artisan migrate 
```

## usage

> **Step 1 :** add trait PaymenterAble to User model .

```
use M74asoud\Paymenter\Services\Payment\Contract\PaymenterAble;

class  User  extends  Authenticatable  {
	use PaymenterAble;
}
```
**PaymenterAble** includes below methods:

```
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\ObjectValue\Money;
use M74asoud\Paymenter\Services\Payment\PaymenterTDO;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
```
 - $user->balance() : Money 
	> balance method return the latest inventory of the user's wallet

 - $user->hasMoney(Money  $money): bool
	> does user hasMoney in wallet

 - $user->getPaymenterTypes(): array
 	> return existing payment method example : wallet - online , ...

 - $user->getOnlinePaymentPortals(): array 
	> return all active bank portals
	
 - $user->pay( PaymenterTDO  $paymenterTDO,  PaymenterTypeInterface  $paymenter_type  =  null ): bill
 - $user->recharge( PaymenterTDO  $paymenterTDO,  PaymenterTypeInterface  $paymenter_type  =  null ): bill

> **Step 2 :** create a custom class any where  and implement PaymenterControllerInterface like below

```
namespace  App\Http\Controllers;
use M74asoud\Paymenter\Services\Payment\Contract\PaymenterControllerInterface;

class  PaymentVerify  implements  PaymenterControllerInterface
{

	public  function  verifyHandler(Bill  $bill)
	{
		// do something you like
	}

}
```
> **Step 3 :** in your project AppServiceProvider Bind PaymenterControllerInterface to your Custom Class

```
public  function  register()
{
	$this->app->bind(PaymenterControllerInterface::class,CustomClass::class);
}
```

   ```
$bill->status :
const  Status  =  [
                    'pending'  	 	 =>  0,
                    'watingPay'  	 =>  1,
                    'paid'  	 	 =>  2,
                    'noEnoughMoney'  =>  3,
                    'error'  		 =>  4
				];


* if use online payment and $bill->status === 1 (watingPay)
you must redirect user to bank portal with link : 
$bill->paymentTransaction->request_link , then verify user payment in your custom class than created.


* when you try to pay or recahrge you must pass an object of PaymenterTDO .
PaymenterTDO argumans  :
	- Money  $amount,
	- string  $description,
	- int  $type ### this arguman Special for you , you can pass any integer and then verify $bill in your custom handler by it;

```
    

