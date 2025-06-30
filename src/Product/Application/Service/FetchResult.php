<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class FetchResult
{
    private function __construct(
        private readonly string $productId,
        private readonly bool $success,
        private readonly ?string $message,
        private readonly ?array $data,
    ) {
    }

    public static function success(string $productId, ?array $data = null): self
    {
        return new self($productId, true, null, $data);
    }

    public static function failure(string $productId, string $message): self
    {
        return new self($productId, false, $message, null);
    }

    public function getProductId(): string
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
