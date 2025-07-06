<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Application\DTO\Request\GetProductPriceByIdRequestDto;
use App\Product\Application\DTO\Response\ProductPriceResponseDto;
use App\Product\Application\UseCase\GetAllProductPricesUseCase;
use App\Product\Application\UseCase\GetProductPriceByIdUseCase;
use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductPriceService
{
    public function __construct(
        private readonly GetAllProductPricesUseCase $getAllUseCase,
        private readonly GetProductPriceByIdUseCase $getByIdUseCase,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getAllPrices(): array
    {
        try {
            $prices = $this->getAllUseCase->execute();
            $responseData = $this->mapProductPrices($prices);

            return $this->sucessResponse($responseData);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to fetch all product prices: '.$e->getMessage(), 0, $e);
        }
    }

    public function getPriceById(string $productId): array
    {
        $this->validateProductId($productId);

        $productIdVO = new ProductId($productId);
        $price = $this->getByIdUseCase->execute($productIdVO);

        if (null === $price) {
            throw new \RuntimeException("Product with ID '{$productId}' not found", 404);
        }

        $responseData = ProductPriceResponseDto::fromEntity($price)->toArray();

        return $this->sucessResponse($responseData);
    }

    private function mapProductPrices(array $prices): array
    {
        return array_map(
            fn (ProductPrice $price) => ProductPriceResponseDto::fromEntity($price)->toArray(),
            $prices
        );
    }

    private function sucessResponse(array $responseData): array
    {
        return [
            'success' => true,
            'data' => $responseData,
            'count' => count($responseData),
        ];
    }

    private function validateProductId(string $productId): void
    {
        $request = new GetProductPriceByIdRequestDto($productId);
        $violations = $this->validator->validate($request);

        if (count($violations) > 0) {
            throw new \InvalidArgumentException('Invalid product ID format');
        }
    }
}
