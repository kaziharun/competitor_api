<?php

declare(strict_types=1);

namespace App\Product\Application\Handler;

use App\Product\Application\Command\FetchPricesCommandData;
use App\Product\Domain\Service\PriceAggregationService;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\VendorName;
use App\Product\Infrastructure\Api\CompetitorApiFactory;

class FetchPricesCommandHandler
{
    public function __construct(
        private readonly PriceAggregationService $aggregationService,
        private readonly CompetitorApiFactory $apiFactory,
    ) {
    }

    public function handle(FetchPricesCommandData $command): void
    {
        try {
            $fetchedAt = FetchedAt::now();
            $productId = $command->getProductId();
            $allApiResults = $this->apiFactory->getCompetitorPricesFromAllApis($productId->getValue());

            $allPrices = [];
            foreach ($allApiResults as $provider => $apiResult) {
                if (null === $apiResult) {
                    continue;
                }

                $normalized = $this->normalizeApiResult($provider, $apiResult);
                $allPrices = array_merge($allPrices, $normalized);
            }

            if (!empty($allPrices)) {
                $priceDataObjects = array_map(
                    fn ($priceArray) => new PriceData(
                        new VendorName($priceArray['vendor']),
                        new Price($priceArray['price'])
                    ),
                    $allPrices
                );

                $this->aggregationService->aggregateAndStoreLowestPrice(
                    $productId,
                    $priceDataObjects,
                    $fetchedAt
                );
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function normalizeApiResult(string $provider, array $apiResult): array
    {
        if (isset($apiResult['competitor_data'])) {
            return array_map(fn ($row) => [
                'vendor' => $row['vendor'],
                'price' => (float) $row['price'],
            ], $apiResult['competitor_data']);
        }

        return [];
    }
}
