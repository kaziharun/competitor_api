<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

final class RequestId
{
    private function __construct(
        private string $value,
    ) {
        $this->validate();
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(): void
    {
        if (empty(trim($this->value))) {
            throw new \InvalidArgumentException('Request ID cannot be empty');
        }

        if (!Uuid::isValid($this->value)) {
            throw new \InvalidArgumentException('Invalid request ID format');
        }
    }
}
