<?php


namespace M74asoud\Paymenter\Models;


use Illuminate\Database\Eloquent\Model;
use M74asoud\Paymenter\Services\UserInstance;

class Wallet extends Model
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = UserInstance::TablePrefix() . '_wallets';
    }

    protected $fillable = ['user_hash', 'before', 'amount', 'balance'];

    public function billPay()
    {
        return $this->morphOne(Bill::class, 'paymenterable');
    }

    public function user()
    {
        return $this->belongsTo(
            UserInstance::UserModelClass(),
            'user_hash',
            UserInstance::UserHashField()
        );
    }

    public function billRecharge()
    {
        return $this->belongsTo(Bill::class,'id','wallet_id');
    }
}
