<?php

namespace M74asoud\Paymenter\Services\Payment;

use M74asoud\Paymenter\ObjectValue\Money;

class PaymenterTDO
{
    private $amount;
    private $description;
    private $type;

    public function __construct( Money $amount, string $description, int $type)
    {
        $this->amount = $amount;
        $this->description = $description;
        $this->type = $type;
    }


    public function getAmount(): Money
    {
        return $this->amount;
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
