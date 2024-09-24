<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use App\Product\Repository\ProductCategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ItemCategoryConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly ProductCategoryRepository $categoryRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ItemCategoryConstraint) {
            throw new UnexpectedTypeException($constraint, ItemCategoryConstraint::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'discount configuration array');
        }

        if (!array_key_exists(DiscountConfig::CATEGORY_ID, $value)) {
            $this->context->buildViolation($constraint->categoryIdUndefined)->addViolation();

            return;
        }

        $categoryId = $value[DiscountConfig::CATEGORY_ID];

        if (!is_string($categoryId)) {
            $this->context->buildViolation($constraint->categoryIdNotString)->addViolation();

            return;
        }

        if (null === $this->categoryRepository->findById($categoryId)) {
            $this->context->buildViolation($constraint->categoryIdNotFound)->addViolation();
        }
    }
}
