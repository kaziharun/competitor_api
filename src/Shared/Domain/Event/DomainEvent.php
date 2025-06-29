<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

interface DomainEvent
{
    public function getOccurredAt(): \DateTimeImmutable;

    public function getEventName(): string;
}
