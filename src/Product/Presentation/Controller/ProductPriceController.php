<?php

declare(strict_types=1);

namespace App\Product\Presentation\Controller;

use App\Product\Application\Exception\ProductPriceExceptionHandler;
use App\Product\Application\Service\ProductPriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/prices')]
class ProductPriceController extends AbstractController
{
    public function __construct(
        private readonly ProductPriceService $apiService,
        private readonly ProductPriceExceptionHandler $errorHandler,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function getAllPrices(): JsonResponse
    {
        return $this->handleApiCall(fn () => $this->apiService->getAllPrices());
    }

    #[Route('/{productId}', methods: ['GET'])]
    public function getPriceById(string $productId): JsonResponse
    {
        return $this->handleApiCall(fn () => $this->apiService->getPriceById($productId));
    }

    private function handleApiCall(callable $apiCall): JsonResponse
    {
        try {
            $response = $apiCall();

            return $this->json($response);
        } catch (\Throwable $e) {
            return $this->createErrorRespone($e);
        }
    }

    private function createErrorRespone(\Throwable $exception): JsonResponse
    {
        $errorResponse = $this->errorHandler->handle($exception);
        $statusCode = $this->errorHandler->getStatusCode($exception);

        return $this->json($errorResponse, $statusCode);
    }
}
