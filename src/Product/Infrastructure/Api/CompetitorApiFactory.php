<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

final class CompetitorApiFactory
{
    private array $apis = [];

    public function __construct()
    {
        $this->initializeApis();
    }

    private function initializeApis(): void
    {
        $this->apis = [
            'competitor_api_1' => new CompetitorApi1(),
            'competitor_api_2' => new CompetitorApi2(),
            'competitor_api_3' => new CompetitorApi3(),
        ];
    }

    public function createAllApis(): array
    {
        return $this->apis;
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

    public function get(string $name): ExternalApiInterface
    {
        if (!isset($this->apis[$name])) {
            throw new \InvalidArgumentException("Competitor API implementation '{$name}' not found");
        }

        return $this->apis[$name];
    }

    public function getAvailableApis(): array
    {
        return array_keys($this->apis);
    }

    public function getHealthyApis(): array
    {
        $healthy = [];

        foreach ($this->apis as $name => $api) {
            if ($api->isAvailable()) {
                $healthy[$name] = $api;
            }
        }

        return $healthy;
    }

    public function getHealthStatus(): array
    {
        $status = [];

        foreach ($this->apis as $name => $api) {
            $status[$name] = [
                'provider' => $api->getProviderName(),
                'available' => $api->isAvailable(),
                'rate_limit' => $api->getRateLimitInfo(),
            ];
        }

        return $status;
    }
}
