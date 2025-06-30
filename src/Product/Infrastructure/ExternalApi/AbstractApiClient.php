<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\ExternalApi;

use App\Product\Domain\ValueObject\ProductId;

abstract class AbstractApiClient
{
    protected const DEFAULT_TIMEOUT = 30;
    protected const DEFAULT_RETRY_ATTEMPTS = 3;
    protected const DEFAULT_RETRY_DELAY = 1000;

    protected function __construct()
    {
    }

    protected function makeRequest(string $url, array $options = []): array
    {
        $defaultOptions = [
            'timeout' => self::DEFAULT_TIMEOUT,
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => 'MetroMarket-API-Client/1.0',
            ],
        ];

        $finalOptions = array_merge($defaultOptions, $options);

        $context = stream_context_create([
            'http' => $finalOptions,
        ]);

        $response = file_get_contents($url, false, $context);

        if (false === $response) {
            throw new \RuntimeException('Failed to make HTTP request to: '.$url);
        }

        $data = json_decode($response, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Failed to decode JSON response: '.json_last_error_msg());
        }

        return $data;
    }

    protected function validateResponse(array $response): bool
    {
        return isset($response['status']) && 'success' === $response['status'];
    }

    protected function extractDataFromResponse(array $response): array
    {
        return $response['data'] ?? [];
    }

    public function fetchPricesForProduct(ProductId $productId): array
    {
        $this->preRequest($productId);

        try {
            $prices = $this->doFetchPrices($productId);

            $this->postRequest($prices);

            return $prices;
        } catch (\Exception $e) {
            $this->onError($productId, $e);

            throw $e;
        }
    }

    protected function preRequest(ProductId $productId): void
    {
    }

    protected function postRequest(array $prices): void
    {
    }

    protected function onError(ProductId $productId, \Exception $e): void
    {
    }

    protected function doFetchPrices(ProductId $productId): array
    {
        throw new \RuntimeException('doFetchPrices method must be implemented');
    }
}
