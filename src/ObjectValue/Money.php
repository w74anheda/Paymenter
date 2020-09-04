<?php


namespace M74asoud\Paymenter\ObjectValue;


class Money
{

    private $value;
    private $currency;

    const CURRENCY_IRR = 'IRR';

    public function __construct(int $value, string $currency = self::CURRENCY_IRR)
    {

        if ($value < 0) {
            throw new \InvalidArgumentException('value must zero or more than');
        }

        $this->value    = $value;
        $this->currency = $currency;
    }


    public function getValue(): int
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $money): Money
    {

        $this->checkCurrency($money);

        if ($money->getValue() === 0) {
            return $this;
        }

        return new Money($this->value + $money->getValue(), $this->currency);
    }

    public function reduce(Money $money): Money
    {

        $this->checkCurrency($money);

        if ($money->getValue() === 0) {
            return $this;
        }
        if ($this->value < $money->getValue()) {
            throw new \InvalidArgumentException('money value greater than current value');
        }

        return new Money($this->value - $money->getValue(), $this->currency);
    }

    public function isFewer(Money $money): bool
    {
        $this->checkCurrency($money);

        return $this->value < $money->getValue();
    }

    public function isMore(Money $money): bool
    {
        $this->checkCurrency($money);

        return $this->value > $money->getValue();
    }

    public function isMoreEqual(Money $money): bool
    {
        $this->checkCurrency($money);

        return $this->value >= $money->getValue();
    }

    public function isFewerEqual(Money $money): bool
    {
        $this->checkCurrency($money);

        return $this->value <= $money->getValue();
    }

    public function equals(Money $money): bool
    {
        return
            $this->value === $money->getValue() &&
            $this->currency === $money->getCurrency();
    }

    private function checkCurrency(Money $money)
    {
        if ($this->currency !== $money->getCurrency()) {
            throw new \InvalidArgumentException('currency not equals', 1001);
        }
    }
}
