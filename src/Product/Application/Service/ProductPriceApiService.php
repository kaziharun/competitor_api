<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Application\DTO\Request\GetProductPriceByIdRequest;
use App\Product\Application\DTO\Response\ProductPriceResponse;
use App\Product\Application\UseCase\CachedGetAllProductPricesUseCase;
use App\Product\Application\UseCase\CachedGetProductPriceByIdUseCase;
use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductPriceApiService
{
    public function __construct(
        private readonly CachedGetAllProductPricesUseCase $getAllUseCase,
        private readonly CachedGetProductPriceByIdUseCase $getByIdUseCase,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getAllPrices(): array
    {
        try {
            $prices = $this->getAllUseCase->execute();

            $responseData = array_map(
                fn (ProductPrice $price) => ProductPriceResponse::fromEntity($price)->toArray(),
                $prices
            );

            return [
                'success' => true,
                'data' => $responseData,
                'count' => count($responseData),
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to fetch all product prices: '.$e->getMessage(), 0, $e);
        }
    }

    public function getProductPriceById(string $productId): array
    {
        $this->validateProductId($productId);

        $productIdValueObject = new ProductId($productId);
        $price = $this->getByIdUseCase->execute($productIdValueObject);

        if (null === $price) {
            throw new \RuntimeException("Product with ID '{$productId}' not found", 404);
        }

        $responseData = ProductPriceResponse::fromEntity($price)->toArray();

        return [
            'success' => true,
            'data' => $responseData,
        ];
    }

    private function validateProductId(string $productId): void
    {
        $request = new GetProductPriceByIdRequest($productId);
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            throw new \InvalidArgumentException('Invalid product ID format');
        }
    }
}
