<?php

namespace Insurance\ValueObjects;

final class Money
{
    private int $amount; 
    private string $currency;

    public function __construct(int $amount, string $currency = 'INR')
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function add(Money $other): Money
    {
        return new Money($this->amount + $other->amount, $this->currency);
    }

    public function multiply(float $factor): Money
    {
        return new Money((int) round($this->amount * $factor), $this->currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
