<?php

declare(strict_types=1);

namespace App\Discount\Factory;

use App\AppCommon\Exception\ViolationException;
use App\Discount\Entity\Discount;
use App\Discount\Entity\DiscountConfig;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Validator\DiscountConfiguration\OrderTotalValueConstraint;

class OrderTotalValueDiscountFactory extends AbstractDiscountFactory
{
    public function create(
        string $code,
        int $priority,
        string $description,
        ApplyType $applyType,
        int $discountValue,
        int $threshold
    ): DiscountInterface {
        $configuration = [
            DiscountConfig::THRESHOLD => $threshold,
        ];

        $discount = new Discount(
            3,
            $code,
            $priority,
            $description,
            DiscountType::ORDER_TOTAL_VALUE,
            $applyType,
            $discountValue,
            $configuration
        );

        $this->validate($discount);

        return $discount;
    }

    public function validate(DiscountInterface $discount): void
    {
        $this->validateDiscountEntity($discount);

        $violations = $this->validator->validate(
            $discount->getConfiguration(),
            [
                new OrderTotalValueConstraint(),
            ]
        );

        if (0 < count($violations)) {
            throw new ViolationException($violations,
                sprintf(
                    'Configuration for discount of type %s is invalid',
                    DiscountType::ORDER_TOTAL_VALUE->value
                ));
        }
    }
}
