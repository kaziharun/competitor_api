<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class FetchResultCollection
{
    /**
     * @var array<FetchResult>
     */
    private array $results = [];

    public function add(FetchResult $result): void
    {
        $this->results[] = $result;
    }

    /**
     * @return array<FetchResult>
     */
    public function getAll(): array
    {
        return $this->results;
    }

    /**
     * @return array<FetchResult>
     */
    public function getSuccessful(): array
    {
        return array_filter($this->results, fn (FetchResult $result) => $result->isSuccess());
    }

    /**
     * @return array<FetchResult>
     */
    public function getFailed(): array
    {
        return array_filter($this->results, fn (FetchResult $result) => $result->isFailure());
    }

    public function getTotalCount(): int
    {
        return count($this->results);
    }

    public function getSuccessCount(): int
    {
        return count($this->getSuccessful());
    }

    public function getFailureCount(): int
    {
        return count($this->getFailed());
    }

    public function hasFailures(): bool
    {
        return $this->getFailureCount() > 0;
    }

    public function isAllSuccessful(): bool
    {
        return 0 === $this->getFailureCount();
    }

    /**
     * @return array<string>
     */
    public function getErrorMessages(): array
    {
        return array_map(
            fn (FetchResult $result) => $result->getFormattedMessage(),
            $this->getFailed()
        );
    }

    /**
     * @return array<string>
     */
    public function getSuccessfulProductIds(): array
    {
        return array_map(
            fn (FetchResult $result) => $result->getProductId(),
            $this->getSuccessful()
        );
    }

    /**
     * @return array<string>
     */
    public function getFailedProductIds(): array
    {
        return array_map(
            fn (FetchResult $result) => $result->getProductId(),
            $this->getFailed()
        );
    }
}
