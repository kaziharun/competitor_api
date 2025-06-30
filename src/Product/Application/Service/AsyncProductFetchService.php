<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\RequestId;
use Symfony\Component\Messenger\MessageBusInterface;

final class AsyncProductFetchService implements ProductFetchServiceInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly DefaultProductIdsService $defaultProductIdsService,
    ) {
    }

    public function fetchPricesForProduct(ProductId $productId): void
    {
        $requestId = new RequestId(uniqid('fetch_', true));
        $message = FetchPricesMessage::create($productId, $requestId);

        $this->messageBus->dispatch($message);
    }

    public function fetchPricesForAllDefaultProducts(): void
    {
        foreach ($this->defaultProductIdsService->getDefaultProductIds() as $productIdString) {
            try {
                $productId = new ProductId($productIdString);
                $this->fetchPricesForProduct($productId);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function fetchSingleProduct(string $productId): FetchResult
    {
        try {
            $productIdValueObject = new ProductId($productId);
            $this->fetchPricesForProduct($productIdValueObject);

            return FetchResult::success($productId);
        } catch (\Exception $e) {
            return FetchResult::failure($productId, $e->getMessage());
        }
    }
}
