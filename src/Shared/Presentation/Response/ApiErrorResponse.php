<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Response;

final class ApiErrorResponse
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly int $status,
        public readonly ?string $details = null,
    ) {
    }

    public static function notFound(string $resource, ?string $details = null): self
    {
        return new self(
            'RESOURCE_NOT_FOUND',
            sprintf('%s not found', ucfirst($resource)),
            404,
            $details
        );
    }

    public static function validationError(string $field, string $message): self
    {
        return new self(
            'VALIDATION_ERROR',
            'Validation failed',
            400,
            sprintf('%s: %s', $field, $message)
        );
    }

    public static function unauthorized(): self
    {
        return new self(
            'UNAUTHORIZED',
            'Invalid or missing API key',
            401,
            null
        );
    }

    public static function internalError(?string $details = null): self
    {
        return new self(
            'INTERNAL_ERROR',
            'An internal error occurred',
            500,
            $details
        );
    }

    /**
     * Create a service unavailable error response.
     *
     * @param string $message Error message
     *
     * @return self Error response instance
     */
    public static function serviceUnavailable(string $message): self
    {
        return new self(
            'SERVICE_UNAVAILABLE',
            'Service Unavailable',
            503,
            $message
        );
    }

    /**
     * Convert error response to array format.
     *
     * @return array<string, mixed> Array representation of the error response
     */
    public function toArray(): array
    {
        $data = [
            'error' => [
                'code' => $this->code,
                'message' => $this->message,
                'status' => $this->status,
            ],
        ];

        if (null !== $this->details) {
            $data['error']['details'] = $this->details;
        }

        return $data;
    }
}
