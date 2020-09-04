<?php

namespace M74asoud\Paymenter\Models;

use Illuminate\Database\Eloquent\Model;
use M74asoud\Paymenter\Services\UserInstance;

class Bill extends Model
{

    const Status = [
        'pending' => 0,
        'watingPay' => 1,
        'paid' => 2,
        'noEnoughMoney' => 3,
        'error' => 4,
    ];

    const ActionType = [
        'payment' => 0,
        'recharge' => 1,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = UserInstance::TablePrefix() . '_bills';
    }

    protected $fillable = [
        'user_hash',
        'hash',
        'status',
        'amount',
        'actionType',
        'wallet_id',
        'paymenterable_id',
        'paymenterable_type',
        'type',
        'description'
    ];

    public function paymenterable()
    {
        return $this->morphTo();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'id', 'wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(
            UserInstance::UserModelClass(),
            'user_hash',
            UserInstance::UserHashField()
        );
    }


    public function setNoEnoghMoney(): self
    {
        $this->update(['status' => self::Status['noEnoughMoney']]);
        return $this;
    }

    public function setPaid(int $paymenterId, string $paymenterType): self
    {
        $this->update([
            'status' => self::Status['paid'],
            'paymenterable_id' => $paymenterId,
            'paymenterable_type' => $paymenterType,
        ]);
        return $this;
    }

    public function setError(): self
    {
        $this->update(['status' => self::Status['error']]);
        return $this;
    }
    public function setWatingPay(): self
    {
        $this->update(['status' => self::Status['watingPay']]);
        return $this;
    }
    public function setWallet(Wallet $wallet): self
    {
        $this->update([
            'wallet_id' => $wallet->id,
            'status' => self::Status['paid']
        ]);
        return $this;
    }

    public function paymentTransaction(){
        return $this->hasOne(PaymentTransaction::class, 'bill_hash', 'hash');
    }
}
