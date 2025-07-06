<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Identifier;

final class ProductId extends Identifier
{
    protected function validate(): void
    {
        parent::validate();

        if (!preg_match('/^[a-zA-Z0-9-_]+$/', $this->value)) {
            throw new \InvalidArgumentException('Product ID can only contain alphanumeric characters, hyphens, and underscores');
        }
    }
}
