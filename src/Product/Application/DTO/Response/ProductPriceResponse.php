<?php

declare(strict_types=1);

namespace App\Product\Application\DTO\Response;

use App\Product\Domain\Entity\ProductPrice;

final class ProductPriceResponse
{
    public function __construct(
        private readonly string $productId,
        private readonly string $vendorName,
        private readonly float $price,
        private readonly string $fetchedAt,
    ) {
    }

    public static function fromEntity(ProductPrice $productPrice): self
    {
        return new self(
            $productPrice->getProductId()->getValue(),
            $productPrice->getVendorName()->getValue(),
            $productPrice->getPrice()->getValue(),
            $productPrice->getFetchedAt()->getValue()->format('Y-m-d H:i:s')
        );
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getFetchedAt(): string
    {
        return $this->fetchedAt;
    }

    /**
     * @return array{product_id: string, vendor_name: string, price: float, fetched_at: string}
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'vendor_name' => $this->vendorName,
            'price' => $this->price,
            'fetched_at' => $this->fetchedAt,
        ];
    }
}
