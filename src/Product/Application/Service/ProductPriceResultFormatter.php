<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class ProductPriceResultFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function formatResult(FetchResult $result): array
    {
        return [
            'productId' => $result->getProductId(),
            'status' => $result->isSuccess() ? 'success' : 'failure',
            'error' => $result->getErrorMessage(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function formatCollection(FetchResultCollection $results): array
    {
        return [
            'summary' => [
                'total' => $results->getTotalCount(),
                'successful' => $results->getSuccessCount(),
                'failed' => $results->getFailureCount(),
            ],
            'results' => array_map(fn (FetchResult $result) => $this->formatResult($result), $results->getAll()),
        ];
    }
}
