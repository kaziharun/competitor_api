<?php

declare(strict_types=1);

namespace App\Product\Application\DTO\Response;

use App\Product\Application\Service\FetchResult;

final class FetchPricesApiResponse
{
    public function __construct(
        public readonly string $productId,
        public readonly string $status,
        public readonly ?string $error = null,
        public readonly ?string $message = null,
    ) {
    }

    public static function fromFetchResult(FetchResult $result): self
    {
        return new self(
            $result->getProductId(),
            $result->isSuccess() ? 'success' : 'failure',
            $result->getErrorMessage(),
            $result->isSuccess() ? 'Prices fetched and aggregated successfully' : null
        );
    }

    /**
     * Convert response to array format.
     *
     * @return array<string, mixed> Array representation of the response
     */
    public function toArray(): array
    {
        $data = [
            'productId' => $this->productId,
            'status' => $this->status,
        ];

        if (null !== $this->error) {
            $data['error'] = $this->error;
        }

        if (null !== $this->message) {
            $data['message'] = $this->message;
        }

        return $data;
    }
}
