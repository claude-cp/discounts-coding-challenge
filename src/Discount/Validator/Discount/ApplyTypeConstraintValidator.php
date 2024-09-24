<?php

declare(strict_types=1);

namespace App\Discount\Validator\Discount;

use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\ApplyType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApplyTypeConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplyTypeConstraint) {
            throw new UnexpectedTypeException($constraint, ApplyTypeConstraint::class);
        }

        if (!$value instanceof DiscountInterface) {
            throw new UnexpectedTypeException($constraint, DiscountInterface::class);
        }

        $applyType = $value->getApplyType();
        $discountValue = $value->getDiscountValue();

        if ($applyType === ApplyType::PERCENT && (1 > $discountValue || 100 < $discountValue)) {
            $this->context
                ->buildViolation($constraint->badDiscountPercentValue)
                ->addViolation();

            return;
        }

        if ($applyType === ApplyType::FIXED && 1 > $discountValue) {
            $this->context
                ->buildViolation($constraint->badDiscountFixedValue)
                ->addViolation();
        }
    }
}
