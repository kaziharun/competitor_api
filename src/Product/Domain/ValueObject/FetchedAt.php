<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class FetchedAt
{
    public function __construct(private \DateTimeImmutable $value)
    {
    }

    public static function now(): self
    {
        return new self(new \DateTimeImmutable());
    }

    public function getValue(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function equals(FetchedAt $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }
}
