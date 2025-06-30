<?php

declare(strict_types=1);

namespace App\Product\Presentation\Validation;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ProductId extends Constraint
{
    public string $message = 'The product ID "{{ value }}" is not valid. Product ID must be 3-50 characters long and contain only letters, numbers, and hyphens.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ProductIdValidator::class;
    }
}
