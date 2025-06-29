<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

/**
 * Value object representing a request ID.
 */
final class RequestId
{
    public function __construct(
        private readonly string $value,
    ) {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Request ID cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(RequestId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
