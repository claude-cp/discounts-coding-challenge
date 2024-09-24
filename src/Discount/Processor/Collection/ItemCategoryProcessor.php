<?php

declare(strict_types=1);

namespace App\Discount\Processor\Collection;

use App\Discount\DTO\Input\ItemInput;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\ItemOutput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\DiscountConfig;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\DiscountType;
use App\Discount\Factory\DiscountOutputFactory;
use App\Discount\Processor\DiscountedValueResolver;
use App\Product\Entity\ProductInterface;
use App\Product\Repository\ProductRepository;

class ItemCategoryProcessor extends AbstractDiscountProcessor
{
    use DiscountedValueResolver;

    public function __construct(
        protected DiscountOutputFactory $discountOutputFactory,
        private readonly ProductRepository $productRepository,
    ){
        parent::__construct($discountOutputFactory);
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::ITEM_CATEGORY;
    }

    public function isApplicable(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): bool
    {
        $discountConfig = $discount->getConfiguration();
        $categoryId = $discountConfig[DiscountConfig::CATEGORY_ID];
        $requiredQty = $discountConfig[DiscountConfig::REQUIRED_QTY];

        /**
         * @var ItemInput
         */
        foreach ($orderInput->getItems() as $inputItem) {
            if ($this->shouldSkip($inputItem->getProductId(), $categoryId)) {
                continue;
            }

            if ($inputItem->getQuantity() > $requiredQty) {
                return true;
            }
        }

        return false;
    }

    public function apply(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): void
    {
        $discountConfig = $discount->getConfiguration();
        $categoryId = $discountConfig[DiscountConfig::CATEGORY_ID];
        $requiredQty = $discountConfig[DiscountConfig::REQUIRED_QTY];

        /** @var ItemOutput $itemOutput */
        foreach ($orderOutput->getItems() as $itemOutput) {
            if ($this->shouldSkip($itemOutput->getProductId(), $categoryId)) {
                continue;
            }

            $totalDiscountValue = 0.0;
            $itemQty = $itemOutput->getQuantity();

            if ($itemQty > $requiredQty) {
                $howManyToDiscount = (int) floor($itemQty / $requiredQty);
                $howManyToDiscount = $itemQty % $requiredQty === 0 ? $howManyToDiscount - 1 : $howManyToDiscount;
                $discountValue = self::resolveDiscountedValue(
                    $itemOutput->getUnitPrice(),
                    $discount
                );

                $discountOutput = $this->discountOutputFactory->create($discount, $discountValue);
                for ($i = 1; $i <= $howManyToDiscount; $i++) {
                    $itemOutput->addDiscount($discountOutput);
                    $totalDiscountValue += $discountValue;
                }

                $itemOutput->setTotal($itemOutput->getTotal() - $totalDiscountValue);
                $orderOutput->setTotal($orderOutput->getTotal() - $totalDiscountValue);
            }
        }
    }

    private function shouldSkip(string $productId, string $desiredCategoryId): bool
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findById($productId);
        if ($desiredCategoryId !== $product->getCategory()) {
            return true;
        }

        return false;
    }
}
