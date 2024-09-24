<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OrderTotalValueConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OrderTotalValueConstraint) {
            throw new UnexpectedTypeException($constraint, OrderTotalValueConstraint::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'discount configuration array');
        }

        if (!array_key_exists(DiscountConfig::THRESHOLD, $value)) {
            $this->context
                ->buildViolation($constraint->missingMinimumOrderTotalValue)
                ->addViolation();

            return;
        }

        $threshold = $value[DiscountConfig::THRESHOLD];
        if (!is_int($threshold) || $threshold < 0) {
            $this->context
                ->buildViolation($constraint->orderTotalValueNotPositiveInteger)
                ->addViolation();
        }
    }
}
