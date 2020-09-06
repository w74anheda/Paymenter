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

> add trait PaymenterAble to User model .

```
use M74asoud\Paymenter\Services\Payment\Contract\PaymenterAble;

class  User  extends  Authenticatable  {
	use PaymenterAble;
}
```
**PaymenterAble** includes below methods:

```
use M74asoud\Paymenter\Models\Bill;
use M74asoud\Paymenter\Services\Payment\PaymenterTDO;
use M74asoud\Paymenter\Services\Payment\Types\Contract\PaymenterTypeInterface;
```
 - $user->balance() : Bill 
	> balance method return the latest inventory of the user's wallet

 - $user->hasMoney(Money  $money): bill
	> does user hasMoney in wallet

 - $user->getPaymenterTypes(): array
 	> return existing payment method example : wallet - online , ...

 - $user->getOnlinePaymentPortals(): array 
	> return all active bank portals
 - $user->pay( PaymenterTDO  $paymenterTDO,  PaymenterTypeInterface  $paymenter_type  =  null ): bill
 - $user->recharge( PaymenterTDO  $paymenterTDO,  PaymenterTypeInterface  $paymenter_type  =  null ): bill
