<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\ProductId;

final class PriceAggregationService
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
    ) {
    }

    public function aggregateAndStoreLowestPrice(ProductId $productId, array $priceData, FetchedAt $fetchedAt): void
    {
        if (empty($priceData)) {
            return;
        }

        $lowestPrice = $this->findLowestPrice($priceData);

        $existingPrice = $this->repository->findByProductId($productId);

        if (null !== $existingPrice) {
            $existingPrice->updatePrice(
                $lowestPrice->getPrice(),
                $lowestPrice->getVendor(),
                $fetchedAt
            );
            $this->repository->save($existingPrice);
        } else {
            $productPrice = new ProductPrice(
                $productId,
                $lowestPrice->getVendor(),
                $lowestPrice->getPrice(),
                $fetchedAt
            );
            $this->repository->save($productPrice);
        }
    }

    private function findLowestPrice(array $priceData): PriceData
    {
        if (empty($priceData)) {
            throw new \InvalidArgumentException('Price data array cannot be empty');
        }

        $lowestPrice = $priceData[0];
        foreach ($priceData as $price) {
            if ($price->getPrice()->getValue() < $lowestPrice->getPrice()->getValue()) {
                $lowestPrice = $price;
            }
        }

        return $lowestPrice;
    }
}
