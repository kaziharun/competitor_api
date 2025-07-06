<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Application\Service\FetchResult;
use App\Product\Domain\Service\DefaultProductIdsService;
use App\Product\Domain\Service\ProductFetchServiceInterface;
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
        $message = FetchPricesMessage::create($productId, RequestId::generate());

        $this->messageBus->dispatch($message);
    }

    public function fetchPricesForAllDefaultProducts(): void
    {
        foreach ($this->defaultProductIdsService->getDefaultProductIds() as $productIdString) {
            try {
                $this->fetchPricesForProduct(new ProductId($productIdString));
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function fetchSingleProduct(string $productId): FetchResult
    {
        $productIdValueObject = new ProductId($productId);

        try {
            $this->fetchPricesForProduct($productIdValueObject);

            return FetchResult::success($productIdValueObject);
        } catch (\Exception $e) {
            return FetchResult::failure($productIdValueObject, $e->getMessage());
        }
    }
}
