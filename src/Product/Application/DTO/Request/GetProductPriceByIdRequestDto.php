<?php

declare(strict_types=1);

namespace App\Product\Application\DTO\Request;

use App\Product\Domain\Validation\ProductId as ProductIdConstraint;
use App\Product\Domain\ValueObject\ProductId;
use Symfony\Component\Validator\Constraints as Assert;

final class GetProductPriceByIdRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product ID is required')]
        #[ProductIdConstraint]
        private readonly string $productId,
    ) {
    }

    public function getProductId(): ProductId
    {
        return new ProductId($this->productId);
    }

    public function getProductIdString(): string
    {
        return $this->productId;
    }
}
