<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\ValueObject\ProductId;

final class FetchResult
{
    private function __construct(
        private readonly ProductId $productId,
        private readonly bool $success,
        private readonly ?string $message,
        private readonly ?array $data,
    ) {
    }

    public static function success(ProductId $productId, ?array $data = null): self
    {
        return new self($productId, true, null, $data);
    }

    public static function failure(ProductId $productId, string $message): self
    {
        return new self($productId, false, $message, null);
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailure(): bool
    {
        return !$this->success;
    }

    public function getFormattedMessage(): string
    {
        if ($this->success) {
            return sprintf('✓ Successfully fetched prices for product %s', $this->productId);
        }

        return sprintf('✗ Failed to fetch prices for product %s: %s', $this->productId, $this->message);
    }
}
