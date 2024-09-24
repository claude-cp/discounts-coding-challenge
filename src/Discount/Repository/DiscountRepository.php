<?php

declare(strict_types=1);

namespace App\Discount\Repository;

use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\ApplyType;
use App\Discount\Factory\ItemCategoryDiscountFactory;
use App\Discount\Factory\ItemCheapestDiscountFactory;
use App\Discount\Factory\OrderTotalValueDiscountFactory;

/**
 * FAKE REPOSITORY
 */
class DiscountRepository
{
    public function __construct(
        private readonly ItemCategoryDiscountFactory $itemCategoryDiscountFactory,
        private readonly ItemCheapestDiscountFactory $itemCheapestDiscountFactory,
        private readonly OrderTotalValueDiscountFactory $orderTotalValueDiscountFactory
    ){
    }

    public function getAllSorted(): array
    {
        $discounts = $this->createOurDummyDiscountsAndImagineTheyComeFromDb();

        usort($discounts, function (DiscountInterface $a, DiscountInterface $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return $discounts;
    }

    private function createOurDummyDiscountsAndImagineTheyComeFromDb(): array
    {
        $discounts = [];

        $discounts[] = $this->itemCategoryDiscountFactory->create(
            '5-items-cat-2-then-1-free',
            700,
            'For every product of category Switches (id 2), when you buy five, you get a sixth for free',
            ApplyType::PERCENT,
            100,
            '2',
            5
        );

        $discounts[] = $this->itemCheapestDiscountFactory->create(
            '2-items-then-cheapest-item-20-off',
            700,
            'For two or more products of category Tools (id 1), a 20% discount on the cheapest product',
            ApplyType::PERCENT,
            20,
            '1',
            2
        );

        $discounts[] = $this->orderTotalValueDiscountFactory->create(
            '1000-eur-then-10-percent-off-on-the-order',
            500,
            'A customer who has already bought for over € 1000, gets a discount of 10% on the whole order',
            ApplyType::PERCENT,
            10,
            1000
        );

        return $discounts;
    }
}
