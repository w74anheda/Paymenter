<?php

namespace M74asoud\Paymenter\Services\Payment;

use M74asoud\Paymenter\ObjectValue\Money;

class PaymenterTDO
{
    private $user_hash;
    private $amount;
    private $description;
    private $type;

    public function __construct(string $user_hash, Money $amount, string $description, int $type)
    {
        $this->user_hash = $user_hash;
        $this->amount = $amount;
        $this->description = $description;
        $this->type = $type;
    }


    public function getAmount(): Money
    {
        return $this->amount;
    }
    public function getUserHash(): string
    {
        return $this->user_hash;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
