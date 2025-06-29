<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\RequestId;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Asynchronous service for fetching product prices.
 *
 * This service dispatches messages to the queue system instead of
 * processing immediately, allowing for background processing.
 */
final class AsyncProductFetchService implements ProductFetchServiceInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function fetchSingleProduct(string $productId): FetchResult
    {
        try {
            $requestId = new RequestId(uniqid('fetch_', true));
            $productIdValue = new ProductId($productId);
            $message = FetchPricesMessage::create($productIdValue, $requestId);

            $this->messageBus->dispatch($message);

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

        return array_values(array_filter($productIds));
    }

    private const DEFAULT_PRODUCT_IDS = [
        'product-001',
        'product-002',
        'product-003',
        'product-004',
        'product-005',
    ];
}
