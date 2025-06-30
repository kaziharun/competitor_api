<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Application\Service\CompetitorPriceService;
use App\Product\Domain\ValueObject\ProductId;

final class FetchCompetitorPricesUseCase
{
    public function __construct(
        private readonly CompetitorPriceService $competitorPriceService,
    ) {
    }

    public function execute(ProductId $productId): array
    {
        try {
            $prices = $this->competitorPriceService->fetchAndAggregatePrices($productId);

            if (empty($prices)) {
                return [];
            }

            return $prices;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
