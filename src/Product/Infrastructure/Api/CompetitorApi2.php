<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

final class CompetitorApi2 implements ExternalApiInterface
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
        return 'Competitor API 2';
    }

    public function isAvailable(): bool
    {
        return rand(1, 100) <= 90;
    }

    public function getRateLimitInfo(): array
    {
        return [
            'limit' => 500,
            'remaining' => rand(300, 500),
            'reset' => time() + 1800,
        ];
    }

    private function getMockData(string $productId): ?array
    {
        $mockProducts = [
            '123' => [
                'id' => '123',
                'competitor_data' => [
                    ['name' => 'VendorOne', 'amount' => 20.49],
                    ['name' => 'VendorTwo', 'amount' => 18.99],
                    ['name' => 'VendorThree', 'amount' => 22.99],
                ],
            ],
            '456' => [
                'id' => '456',
                'competitor_data' => [
                    ['name' => 'VendorOne', 'amount' => 30.49],
                    ['name' => 'VendorFour', 'amount' => 28.99],
                    ['name' => 'VendorFive', 'amount' => 32.50],
                ],
            ],
            '999' => [
                'id' => '999',
                'competitor_data' => [
                    ['name' => 'VendorTwo', 'amount' => 12.99],
                    ['name' => 'VendorSix', 'amount' => 11.99],
                    ['name' => 'VendorSeven', 'amount' => 13.50],
                ],
            ],
        ];

        return $mockProducts[$productId] ?? null;
    }
}
