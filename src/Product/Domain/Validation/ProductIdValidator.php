<?php

declare(strict_types=1);

namespace App\Product\Domain\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ProductIdValidator extends ConstraintValidator
{
    private const PATTERN = '/^[a-z0-9-_]{3,50}$/i';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductId) {
            throw new UnexpectedTypeException($constraint, ProductId::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Expected string, got %s', get_debug_type($value)));
        }

        if (!preg_match(self::PATTERN, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(ProductId::ERROR_INVALID_FORMAT)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
