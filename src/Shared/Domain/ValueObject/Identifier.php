<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class Identifier
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Identifier $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    protected function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Identifier cannot be empty');
        }
    }
}
