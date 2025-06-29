<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

interface ProductFetchServiceInterface
{
    /**
     * Fetch prices for a single product.
     *
     * @param string $productId The product ID to fetch prices for
     *
     * @return FetchResult The result of the fetch operation
     */
    public function fetchSingleProduct(string $productId): FetchResult;

    /**
     * Fetch prices for multiple products.
     *
     * @param array<string> $productIds Array of product IDs to fetch prices for
     *
     * @return FetchResultCollection Collection of fetch results
     */
    public function fetchMultipleProducts(array $productIds): FetchResultCollection;

    /**
     * Fetch prices for all default products.
     *
     * @return FetchResultCollection Collection of fetch results
     */
    public function fetchAllDefaultProducts(): FetchResultCollection;

    /**
     * Get default product IDs.
     *
     * @return array<string> Array of default product IDs
     */
    public function getDefaultProductIds(): array;

    /**
     * Parse comma-separated product IDs string.
     *
     * @param string $productsString Comma-separated string of product IDs
     *
     * @return array<string> Array of parsed product IDs
     */
    public function parseProductIds(string $productsString): array;
}
