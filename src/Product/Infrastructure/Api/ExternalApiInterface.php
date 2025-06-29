<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

/**
 * Interface for external competitor price APIs.
 *
 * This interface defines the contract for fetching competitor prices
 * from different external APIs with varying JSON structures.
 */
interface ExternalApiInterface
{
    /**
     * Get competitor prices for a specific product.
     *
     * @param ProductId $productId The product ID to fetch prices for
     *
     * @return array<string, mixed>|null Competitor price data or null if not found
     *
     * @throws \Exception When API request fails
     */
    public function getCompetitorPrices(ProductId $productId): ?array;

    /**
     * Get the API provider name.
     *
     * @return string The name of the API provider
     */
    public function getProviderName(): string;

    /**
     * Check if the API is available/healthy.
     *
     * @return bool True if the API is available
     */
    public function isAvailable(): bool;

    /**
     * Get API rate limit information.
     *
     * @return array<string, mixed> Rate limit information
     */
    public function getRateLimitInfo(): array;
}
