<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class Identifier
{
    public function __construct(protected string $value)
    {
        $this->validate();
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

    protected function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException('Identifier cannot be empty');
        }
    }
}
