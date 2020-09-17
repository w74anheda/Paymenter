<?php

namespace M74asoud\Paymenter\Models;

use Illuminate\Database\Eloquent\Model;
use M74asoud\Paymenter\Services\UserInstance;

class PaymentTransaction extends Model
{

    protected $attributes = [
        'additional'  => '{}',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = UserInstance::TablePrefix() . '_transaction';
    }

    const STATUS = [
        'pending' => 0,
        'waitingVerify' => 1,
        'paid' => 2,
        'cancel' => 3,
        'failed' => 4,
    ];

    protected $fillable = [
        'user_hash',
        'bill_hash',
        'request_link',
        'amount',
        'refNum',
        'resNum',
        'status',
        'additional',
        'portal'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_hash', 'hash');
    }

    public function requestPay(): string
    {
        return route('paymenter.request.link', $this->resNum);
    }

    public function setFaild(array $error = []): self
    {
        $this->update([
            'status' => self::STATUS['failed'],
            'additional' => json_encode($error)
        ]);
        return $this;
    }

    public function setCancel(): self
    {
        $this->update(['status' => self::STATUS['cancel']]);
        return $this;
    }

    public function setWaitingVerify(array $data = []): self
    {
        $this->update(array_merge(
            ['status' => self::STATUS['waitingVerify']],
            $data
        ));
        return $this;
    }
    
    public function setPaid(array $data = []): self
    {
        $this->update(array_merge(
            ['status' => self::STATUS['paid']],
            $data
        ));
        return $this;
    }
}
