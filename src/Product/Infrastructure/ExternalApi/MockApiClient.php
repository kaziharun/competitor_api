<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\ExternalApi;

use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\VendorName;

final class MockApiClient implements ExternalApiClientInterface
{
    public function __construct()
    {
    }

    public function fetchPricesForProduct(ProductId $productId): array
    {
        $mockData = $this->getMockData($productId->getValue());

        if (null === $mockData) {
            return [];
        }

        return array_map(
            fn ($priceData) => new PriceData(
                new VendorName($priceData['vendor']),
                new Price($priceData['price'])
            ),
            $mockData
        );
    }

    public function getProviderName(): string
    {
        return 'Mock API Client';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function getRateLimitInfo(): array
    {
        return [
            'limit' => 1000,
            'remaining' => 999,
            'reset' => time() + 3600,
        ];
    }

    private function getMockData(string $productId): ?array
    {
        $mockProducts = [
            '123' => [
                ['vendor' => 'MockVendor1', 'price' => 19.99],
                ['vendor' => 'MockVendor2', 'price' => 18.50],
                ['vendor' => 'MockVendor3', 'price' => 21.00],
            ],
            '456' => [
                ['vendor' => 'MockVendor1', 'price' => 29.99],
                ['vendor' => 'MockVendor4', 'price' => 27.50],
                ['vendor' => 'MockVendor5', 'price' => 31.00],
            ],
        ];

        return $mockProducts[$productId] ?? null;
    }
}
