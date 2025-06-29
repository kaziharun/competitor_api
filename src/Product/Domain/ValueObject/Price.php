<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class Price
{
    private float $value;

    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }

        $this->value = $value;
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

    public function add(Price $other): Price
    {
        return new self($this->value + $other->value);
    }

    public function subtract(Price $other): Price
    {
        $result = $this->value - $other->value;
        if ($result < 0) {
            throw new \InvalidArgumentException('Price cannot be negative');
        }

        return new self($result);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
