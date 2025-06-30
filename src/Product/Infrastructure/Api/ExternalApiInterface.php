<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Api;

use App\Product\Domain\ValueObject\ProductId;

interface ExternalApiInterface
{
    public function getCompetitorPrices(ProductId $productId): ?array;

    public function getProviderName(): string;

    public function isAvailable(): bool;

    public function getRateLimitInfo(): array;
} 