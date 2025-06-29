<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\ExternalApi;

use App\Product\Domain\ValueObject\ProductId;

final class RetryableApiClient implements ExternalApiClientInterface
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 1000;

    public function __construct(
        private readonly ExternalApiClientInterface $apiClient,
    ) {
    }

    public function fetchPricesForProduct(ProductId $productId): array
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; ++$attempt) {
            try {
                return $this->apiClient->fetchPricesForProduct($productId);
            } catch (\Exception $e) {
                $lastException = $e;

                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                }
            }
        }

        throw $lastException;
    }

    public function getProviderName(): string
    {
        return $this->apiClient->getProviderName();
    }

    public function isAvailable(): bool
    {
        return $this->apiClient->isAvailable();
    }

    public function getRateLimitInfo(): array
    {
        return $this->apiClient->getRateLimitInfo();
    }
}
