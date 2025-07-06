<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Service;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Service\DefaultProductIdsService;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\VendorName;
use App\Product\Infrastructure\Api\Factory\CompetitorApiFactory;

final class CompetitorPriceService
{
    public function __construct(
        private readonly CompetitorApiFactory $apiFactory,
        private readonly DefaultProductIdsService $defaultProductIdsService,
    ) {
    }

    public function fetchAndAggregatePrices(?ProductId $productId = null): array
    {
        $aggregatedPrices = [];

        foreach ($this->getProductIdsToFetch($productId) as $id) {
            $productIdObj = new ProductId($id);

            try {
                $lowestPrice = $this->getLowestCompetitorPrice($productIdObj);

                if (null !== $lowestPrice) {
                    $aggregatedPrices[] = new ProductPrice(
                        $productIdObj,
                        $lowestPrice->getVendor(),
                        $lowestPrice->getPrice(),
                        FetchedAt::now()
                    );
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return $aggregatedPrices;
    }

    private function getProductIdsToFetch(?ProductId $productId): array
    {
        return $productId
            ? [$productId->getValue()]
            : $this->defaultProductIdsService->getDefaultProductIds();
    }

    private function getLowestCompetitorPrice(ProductId $productId): ?PriceData
    {
        $priceDataList = $this->fetchPricesFromAllApis($productId);

        return [] !== $priceDataList
            ? $this->findLowestPrice($priceDataList)
            : null;
    }

    private function fetchPricesFromAllApis(ProductId $productId): array
    {
        $results = $this->apiFactory->getCompetitorPricesFromAllApis($productId->getValue());
        $prices = [];

        foreach ($results as $apiName => $result) {
            if (is_array($result)) {
                $prices = [...$prices, ...$this->extractPricesFromApiResult($result, $apiName)];
            }
        }

        return $prices;
    }

    private function extractPricesFromApiResult(array $result, string $apiName): array
    {
        return match (true) {
            isset($result['prices']) => $this->mapPriceEntries($result['prices'], 'vendor', 'price'),
            isset($result['competitor_data']) => $this->mapPriceEntries($result['competitor_data'], 'name', 'amount'),
            isset($result['market_prices']) => $this->mapPriceEntries($result['market_prices'], 'competitor', 'value'),
            default => [],
        };
    }

    private function mapPriceEntries(array $entries, string $vendorKey, string $priceKey): array
    {
        $priceData = [];

        foreach ($entries as $entry) {
            if (isset($entry[$vendorKey], $entry[$priceKey])) {
                $priceData[] = new PriceData(
                    new VendorName($entry[$vendorKey]),
                    new Price((float) $entry[$priceKey])
                );
            }
        }

        return $priceData;
    }

    private function findLowestPrice(array $prices): PriceData
    {
        return array_reduce(
            $prices,
            fn (PriceData $lowest, PriceData $current) => $current->getPrice()->getValue() < $lowest->getPrice()->getValue() ? $current : $lowest,
            $prices[0] ?? throw new \RuntimeException('No prices found')
        );
    }
}
