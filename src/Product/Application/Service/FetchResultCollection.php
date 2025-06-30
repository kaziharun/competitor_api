<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class FetchResultCollection
{
    private array $results = [];

    public function add(FetchResult $result): void
    {
        $this->results[] = $result;
    }

    public function getAll(): array
    {
        return $this->results;
    }
}
