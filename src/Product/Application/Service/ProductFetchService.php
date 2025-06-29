<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Application\Command\FetchPricesCommandData;
use App\Product\Application\Handler\FetchPricesCommandHandler;
use App\Product\Domain\ValueObject\ProductId;

/**
 * Service for fetching product prices from external APIs.
 *
 * This service coordinates the fetching of product prices from multiple sources
 * and aggregates the results using various strategies.
 */
final class ProductFetchService implements ProductFetchServiceInterface
{
    /**
     * @var array<string> List of product IDs to fetch prices for when no specific product is provided
     */
    private const DEFAULT_PRODUCT_IDS = [
        'product-001',
        'product-002',
        'product-003',
        'product-004',
        'product-005',
    ];

    /**
     * Constructor for ProductFetchService.
     *
     * @param FetchPricesCommandHandler $commandHandler The command handler for executing fetch operations
     */
    public function __construct(
        private readonly FetchPricesCommandHandler $commandHandler,
    ) {
    }

    public function fetchSingleProduct(string $productId): FetchResult
    {
        try {
            $commandData = new FetchPricesCommandData(new ProductId($productId));
            $this->commandHandler->handle($commandData);

            return FetchResult::success($productId);
        } catch (\Exception $e) {
            return FetchResult::failure($productId, $e->getMessage());
        }
    }

    public function fetchMultipleProducts(array $productIds): FetchResultCollection
    {
        $results = new FetchResultCollection();

        foreach ($productIds as $productId) {
            $results->add($this->fetchSingleProduct($productId));
        }

        return $results;
    }

    public function fetchAllDefaultProducts(): FetchResultCollection
    {
        return $this->fetchMultipleProducts(self::DEFAULT_PRODUCT_IDS);
    }

    public function getDefaultProductIds(): array
    {
        return self::DEFAULT_PRODUCT_IDS;
    }

    public function parseProductIds(string $productsString): array
    {
        $productIds = array_map('trim', explode(',', $productsString));

        return array_values(array_filter($productIds)); // Remove empty values and reindex
    }
}
