<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class FetchResult
{
    private function __construct(
        private readonly string $productId,
        private readonly bool $success,
        private readonly ?string $errorMessage = null,
    ) {
    }

    public static function success(string $productId): self
    {
        return new self($productId, true);
    }

    public static function failure(string $productId, string $errorMessage): self
    {
        return new self($productId, false, $errorMessage);
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

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getFormattedMessage(): string
    {
        if ($this->success) {
            return sprintf('Product %s: Success', $this->productId);
        }

        return sprintf('Product %s: %s', $this->productId, $this->errorMessage);
    }
}
