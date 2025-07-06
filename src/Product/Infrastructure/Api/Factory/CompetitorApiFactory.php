<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api\Factory;

use App\Product\Domain\Service\ExternalApiInterface;
use App\Product\Domain\ValueObject\ProductId;

final class CompetitorApiFactory
{
    private array $apis = [];

    public function __construct(iterable $competitorApis)
    {
        foreach ($competitorApis as $api) {
            $this->register($api->getProviderName(), $api);
        }
    }

    public function getCompetitorPricesFromAllApis(string $productId): array
    {
        $results = [];

        foreach ($this->apis as $apiName => $api) {
            try {
                $prices = $api->getCompetitorPrices(new ProductId($productId));
                $results[$apiName] = $prices;
            } catch (\Exception $e) {
                $results[$apiName] = null;
            }
        }

        return $results;
    }

    public function register(string $name, ExternalApiInterface $api): void
    {
        $this->apis[$name] = $api;
    }
}
