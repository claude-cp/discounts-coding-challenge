<?php

declare(strict_types=1);

namespace App\Discount\Factory;

use App\AppCommon\Exception\ViolationException;
use App\Discount\Entity\Discount;
use App\Discount\Entity\DiscountConfig;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Validator\DiscountConfiguration;

class ItemCheapestDiscountFactory extends AbstractDiscountFactory
{
    public function create(
        string $code,
        int $priority,
        string $description,
        ApplyType $applyType,
        int $discountValue,
        string $categoryId,
        int $requiredQty,
    ): DiscountInterface
    {
        $configuration = [
            DiscountConfig::CATEGORY_ID => $categoryId,
            DiscountConfig::REQUIRED_QTY => $requiredQty,
        ];

        $discount = new Discount(
            1,
            $code,
            $priority,
            $description,
            DiscountType::ITEM_CHEAPEST,
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
                new DiscountConfiguration\ItemCategoryConstraint(),
                new DiscountConfiguration\RequiredQtyConstraint(),
            ]
        );

        if (0 < count($violations)) {
            throw new ViolationException($violations,
                sprintf(
                    'Configuration for discount of type %s is invalid',
                    DiscountType::ITEM_CHEAPEST->value
                ));
        }
    }
}
