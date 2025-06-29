<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

class CompetitorApiFactory
{
    private array $apis = [];

    public function __construct()
    {
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

    public function getCompetitorPricesFromAllApis(string $productId): array
    {
        $results = [];

        foreach ($this->apis as $name => $api) {
            if (!$api->isAvailable()) {
                $results[$name] = null;
                continue;
            }

            try {
                $prices = $api->getCompetitorPrices(new \App\Product\Domain\ValueObject\ProductId($productId));
                $results[$name] = $prices;
            } catch (\Exception $e) {
                $results[$name] = null;
            }
        }

        return $results;
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
