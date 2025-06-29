<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Infrastructure\Api\CompetitorApiFactory;

class CompetitorPriceService
{
    private const DEFAULT_PRODUCT_IDS = ['123', '456', '789'];

    public function __construct(
        private readonly CompetitorApiFactory $apiFactory,
    ) {
    }

    /**
     * @param ProductId|null $productId Optional product ID to fetch prices for a specific product
     *
     * @return array<ProductPrice>
     */
    public function fetchAndAggregatePrices(?ProductId $productId = null): array
    {
        $allPrices = [];
        $productIds = $productId ? [$productId->getValue()] : self::DEFAULT_PRODUCT_IDS;

        foreach ($productIds as $productIdString) {
            try {
                $productIdObj = new ProductId($productIdString);
                $prices = $this->fetchPricesFromAllApis($productIdObj);

                if (!empty($prices)) {
                    $lowestPrice = $this->findLowestPrice($prices);
                    $productPrice = new ProductPrice(
                        $productIdObj,
                        $lowestPrice->getVendor(),
                        $lowestPrice->getPrice(),
                        FetchedAt::now()
                    );
                    $allPrices[] = $productPrice;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $allPrices;
    }

    /**
     * Fetch prices from all available APIs and convert to PriceData objects.
     *
     * @param ProductId $productId Product ID to fetch prices for
     *
     * @return array<PriceData> Array of price data from all APIs
     */
    private function fetchPricesFromAllApis(ProductId $productId): array
    {
        $allPriceData = [];
        $apiResults = $this->apiFactory->getCompetitorPricesFromAllApis($productId->getValue());

        foreach ($apiResults as $apiName => $result) {
            if (null === $result) {
                continue;
            }

            $prices = $this->extractPricesFromApiResult($result, $apiName);
            $allPriceData = array_merge($allPriceData, $prices);
        }

        return $allPriceData;
    }

    /**
     * Extract price data from API result based on the API format.
     *
     * @param array<string, mixed> $result  API result data
     * @param string               $apiName Name of the API for logging
     *
     * @return array<PriceData> Array of price data
     */
    private function extractPricesFromApiResult(array $result, string $apiName): array
    {
        $prices = [];

        if (isset($result['prices'])) {
            foreach ($result['prices'] as $priceData) {
                $prices[] = new PriceData(
                    new \App\Product\Domain\ValueObject\VendorName($priceData['vendor']),
                    new \App\Product\Domain\ValueObject\Price($priceData['price'])
                );
            }
        } elseif (isset($result['competitor_data'])) {
            foreach ($result['competitor_data'] as $priceData) {
                $prices[] = new PriceData(
                    new \App\Product\Domain\ValueObject\VendorName($priceData['name']),
                    new \App\Product\Domain\ValueObject\Price($priceData['amount'])
                );
            }
        } elseif (isset($result['market_prices'])) {
            foreach ($result['market_prices'] as $priceData) {
                $prices[] = new PriceData(
                    new \App\Product\Domain\ValueObject\VendorName($priceData['competitor']),
                    new \App\Product\Domain\ValueObject\Price($priceData['value'])
                );
            }
        }

        return $prices;
    }

    /**
     * @param PriceData[] $prices
     */
    private function findLowestPrice(array $prices): PriceData
    {
        if (empty($prices)) {
            throw new \RuntimeException('No prices found');
        }

        $lowestPrice = $prices[0];
        foreach ($prices as $price) {
            if ($price->getPrice()->getValue() < $lowestPrice->getPrice()->getValue()) {
                $lowestPrice = $price;
            }
        }

        return $lowestPrice;
    }
}
