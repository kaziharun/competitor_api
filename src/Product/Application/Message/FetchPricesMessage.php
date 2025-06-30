<?php

declare(strict_types=1);

namespace App\Product\Application\Message;

use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\RequestId;

final class FetchPricesMessage
{
    public function __construct(
        private readonly ProductId $productId,
        private readonly RequestId $requestId,
        private readonly \DateTimeImmutable $requestedAt,
    ) {
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getRequestId(): RequestId
    {
        return $this->requestId;
    }

    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public static function create(ProductId $productId, RequestId $requestId): self
    {
        return new self(
            $productId,
            $requestId,
            new \DateTimeImmutable()
        );
    }
}
