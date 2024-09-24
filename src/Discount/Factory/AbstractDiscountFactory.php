<?php

declare(strict_types=1);

namespace App\Discount\Factory;

use App\AppCommon\Exception\ViolationException;
use App\Discount\Validator\Discount as DiscountAssert;
use App\Discount\Entity\DiscountInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDiscountFactory
{
    public function __construct(protected readonly ValidatorInterface $validator)
    {
    }

    protected function validateDiscountEntity(DiscountInterface $discount): void
    {
        $violations = $this->validator->validate(
            $discount,
            [
                new DiscountAssert\ApplyTypeConstraint(),
            ]
        );

        if (0 < count($violations)) {
            throw new ViolationException($violations);
        }
    }

}
