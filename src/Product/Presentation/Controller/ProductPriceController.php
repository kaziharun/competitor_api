<?php

declare(strict_types=1);

namespace App\Product\Presentation\Controller;

use App\Product\Application\Service\ProductPriceApiService;
use App\Product\Application\Service\ProductPriceErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/prices')]
final class ProductPriceController extends AbstractController
{
    public function __construct(
        private readonly ProductPriceApiService $apiService,
        private readonly ProductPriceErrorHandler $errorHandler,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function getAllPrices(): JsonResponse
    {
        try {
            $response = $this->apiService->getAllPrices();

            return $this->json($response);
        } catch (\Throwable $e) {
            $errorResponse = $this->errorHandler->handleException($e);
            $statusCode = $this->errorHandler->getStatusCode($e);

            return $this->json($errorResponse, $statusCode);
        }
    }

    #[Route('/{productId}', methods: ['GET'])]
    public function getProductPriceById(string $productId): JsonResponse
    {
        try {
            $response = $this->apiService->getProductPriceById($productId);

            return $this->json($response);
        } catch (\Throwable $e) {
            $errorResponse = $this->errorHandler->handleException($e);
            $statusCode = $this->errorHandler->getStatusCode($e);

            return $this->json($errorResponse, $statusCode);
        }
    }
}
