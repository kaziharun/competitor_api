<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

final class CompetitorApi3 implements ExternalApiInterface
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
        return 'Competitor API 3';
    }

    public function isAvailable(): bool
    {
        return rand(1, 100) <= 85;
    }

    public function getRateLimitInfo(): array
    {
        return [
            'limit' => 200,
            'remaining' => rand(100, 200),
            'reset' => time() + 900,
        ];
    }

    private function getMockData(string $productId): ?array
    {
        $mockProducts = [
            '123' => [
                'product' => '123',
                'market_prices' => [
                    ['competitor' => 'StoreX', 'value' => 25.99],
                    ['competitor' => 'StoreY', 'value' => 23.50],
                    ['competitor' => 'StoreZ', 'value' => 27.99],
                ],
            ],
            '789' => [
                'product' => '789',
                'market_prices' => [
                    ['competitor' => 'StoreX', 'value' => 18.99],
                    ['competitor' => 'StoreW', 'value' => 17.50],
                    ['competitor' => 'StoreV', 'value' => 19.99],
                ],
            ],
            '111' => [
                'product' => '111',
                'market_prices' => [
                    ['competitor' => 'StoreY', 'value' => 45.99],
                    ['competitor' => 'StoreZ', 'value' => 43.50],
                    ['competitor' => 'StoreA', 'value' => 47.99],
                ],
            ],
        ];

        return $mockProducts[$productId] ?? null;
    }
}
