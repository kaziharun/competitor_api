<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Shared\Presentation\Response\ApiErrorResponse;

final class ProductPriceErrorHandler
{
    public function handleException(\Throwable $exception): array
    {
        if ($exception instanceof \InvalidArgumentException) {
            $errorResponse = ApiErrorResponse::validationError('productId', $exception->getMessage());
        } elseif ($exception instanceof \RuntimeException && 404 === $exception->getCode()) {
            $errorResponse = ApiErrorResponse::notFound('product price', $exception->getMessage());
        } else {
            $errorResponse = ApiErrorResponse::internalError($exception->getMessage());
        }

        return $errorResponse->toArray();
    }

    public function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof \InvalidArgumentException) {
            return 400;
        } elseif ($exception instanceof \RuntimeException && 404 === $exception->getCode()) {
            return 404;
        } else {
            return 500;
        }
    }
}
