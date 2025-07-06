<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class Price
{
    public function __construct(private float $value)
    {
        if ($this->value < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function isLowerThan(Price $other): bool
    {
        return $this->value < $other->value;
    }

    public function equals(Price $other): bool
    {
        return $this->value === $other->value;
    }
}
