<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class VendorName
{
    public function __construct(private string $value)
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException('Vendor name cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(VendorName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
