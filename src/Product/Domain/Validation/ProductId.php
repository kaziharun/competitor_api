<?php

declare(strict_types=1);

namespace App\Product\Domain\Validation;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ProductId extends Constraint
{
    public const ERROR_INVALID_FORMAT = 'product_id_invalid_format';
    public string $message = 'Invalid product ID "{{ value }}". Use 3–50 letters, numbers, or hyphens.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ProductIdValidator::class;
    }
}
