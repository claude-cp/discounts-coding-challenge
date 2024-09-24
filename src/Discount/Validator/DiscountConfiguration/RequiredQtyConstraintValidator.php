<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequiredQtyConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RequiredQtyConstraint) {
            throw new UnexpectedTypeException($constraint, RequiredQtyConstraint::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'discount configuration array');
        }

        if (!array_key_exists(DiscountConfig::REQUIRED_QTY, $value)) {
            $this->context->buildViolation($constraint->requiredQtyUndefined)->addViolation();

            return;
        }

        $requiredQty = $value[DiscountConfig::REQUIRED_QTY];

        if (!is_int($requiredQty) || 1 > $requiredQty) {
            $this->context->buildViolation($constraint->requiredQtyInteger)->addViolation();
        }
    }
}
