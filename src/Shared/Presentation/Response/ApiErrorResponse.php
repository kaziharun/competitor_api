<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Response;

final class ApiErrorResponse
{
    private const CODE_RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    private const CODE_VALIDATION_ERROR = 'VALIDATION_ERROR';
    private const CODE_UNAUTHORIZED = 'UNAUTHORIZED';
    private const CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
    private const CODE_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    private function __construct(
        private readonly string $code,
        private readonly string $message,
        private readonly int $status,
        private readonly ?string $details = null,
    ) {
    }

    public static function notFound(string $resource, ?string $details = null): self
    {
        return new self(
            self::CODE_RESOURCE_NOT_FOUND,
            sprintf('%s not found', ucfirst($resource)),
            404,
            $details
        );
    }

    public static function validationError(string $field, string $errorMessage): self
    {
        return new self(
            self::CODE_VALIDATION_ERROR,
            'Validation failed',
            400,
            sprintf('%s: %s', $field, $errorMessage)
        );
    }

    public static function unauthorized(): self
    {
        return new self(
            self::CODE_UNAUTHORIZED,
            'Invalid or missing API key',
            401
        );
    }

    public static function internalError(?string $details = null): self
    {
        return new self(
            self::CODE_INTERNAL_ERROR,
            'An internal error occurred',
            500,
            $details
        );
    }

    public static function serviceUnavailable(string $details): self
    {
        return new self(
            self::CODE_SERVICE_UNAVAILABLE,
            'Service unavailable',
            503,
            $details
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'error' => array_filter([
                'code' => $this->code,
                'message' => $this->message,
                'status' => $this->status,
                'details' => $this->details,
            ]),
        ]);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }
}
