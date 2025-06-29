<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class FetchedAt
{
    private \DateTimeImmutable $value;

    public function __construct(\DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function now(): self
    {
        return new self(new \DateTimeImmutable());
    }

    public function getValue(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function isOlderThan(\DateInterval $interval): bool
    {
        $cutoff = (new \DateTimeImmutable())->sub($interval);

        return $this->value < $cutoff;
    }

    public function equals(FetchedAt $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }
}
