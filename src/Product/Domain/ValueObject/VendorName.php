<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class VendorName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Vendor name cannot be empty');
        }

        $this->value = trim($value);
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
