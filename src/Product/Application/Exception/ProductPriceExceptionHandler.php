<?php

declare(strict_types=1);

namespace App\Product\Application\Exception;

use App\Shared\Presentation\Response\ApiErrorResponse;

final class ProductPriceExceptionHandler
{
    public function handle(\Throwable $exception): array
    {
        return $this->buildErrorResponse($exception)->toArray();
    }

    private function buildErrorResponse(\Throwable $exception): ApiErrorResponse
    {
        return match (true) {
            $exception instanceof \InvalidArgumentException => ApiErrorResponse::validationError('productId', $exception->getMessage()),

            $exception instanceof \RuntimeException && 404 === $exception->getCode() => ApiErrorResponse::notFound('product price', $exception->getMessage()),

            default => ApiErrorResponse::internalError($exception->getMessage()),
        };
    }

    public function getStatusCode(\Throwable $exception): int
    {
        return match (true) {
            $exception instanceof \InvalidArgumentException => 400,
            $exception instanceof \RuntimeException && 404 === $exception->getCode() => 404,
            default => 500,
        };
    }
}
