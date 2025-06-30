<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

final class CompetitorApi1 implements ExternalApiInterface
{
    public function __construct()
    {
    }

    public function getCompetitorPrices(ProductId $productId): ?array
    {
        try {
            $mockData = $this->getMockData($productId->getValue());

            if (null === $mockData) {
                return null;
            }

            return $mockData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getProviderName(): string
    {
        return 'Competitor API 1';
    }

    public function isAvailable(): bool
    {
        return rand(1, 100) <= 95;
    }

    public function getRateLimitInfo(): array
    {
        return [
            'limit' => 1000,
            'remaining' => rand(800, 1000),
            'reset' => time() + 3600,
        ];
    }

    private function getMockData(string $productId): ?array
    {
        $mockProducts = [
            '123' => [
                'product_id' => '123',
                'prices' => [
                    ['vendor' => 'ShopA', 'price' => 19.99],
                    ['vendor' => 'ShopB', 'price' => 17.49],
                    ['vendor' => 'ShopC', 'price' => 21.99],
                ],
            ],
            '456' => [
                'product_id' => '456',
                'prices' => [
                    ['vendor' => 'ShopA', 'price' => 29.99],
                    ['vendor' => 'ShopD', 'price' => 27.99],
                    ['vendor' => 'ShopE', 'price' => 31.50],
                ],
            ],
            '789' => [
                'product_id' => '789',
                'prices' => [
                    ['vendor' => 'ShopB', 'price' => 15.99],
                    ['vendor' => 'ShopF', 'price' => 14.99],
                    ['vendor' => 'ShopG', 'price' => 16.50],
                ],
            ],
            '999' => [
                'product_id' => '999',
                'prices' => [
                    ['vendor' => 'MetroStore', 'price' => 24.99],
                    ['vendor' => 'QuickMart', 'price' => 22.49],
                    ['vendor' => 'SuperDeals', 'price' => 26.99],
                ],
            ],
        ];

        return $mockProducts[$productId] ?? null;
    }
}
