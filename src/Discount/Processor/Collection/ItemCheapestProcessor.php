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

class ItemCheapestProcessor extends AbstractDiscountProcessor
{
    use DiscountedValueResolver;

    public function __construct(
        protected DiscountOutputFactory $discountOutputFactory,
        private readonly ProductRepository $productRepository,
    ){
        parent::__construct($this->discountOutputFactory);
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::ITEM_CHEAPEST;
    }

    public function isApplicable(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): bool
    {
        $discountConfig = $discount->getConfiguration();
        $categoryId = $discountConfig[DiscountConfig::CATEGORY_ID];
        $requiredQty = $discountConfig[DiscountConfig::REQUIRED_QTY];

        $totalQtyInCategory = 0;

        /**
         * @var ItemInput
         */
        foreach ($orderInput->getItems() as $inputItem) {
            /** @var ProductInterface $product */
            $product = $this->productRepository->findById($inputItem->getProductId());
            if ($categoryId !== $product->getCategory()) {
                continue;
            }

            /** @var ProductInterface $product */
            $product = $this->productRepository->findById($inputItem->getProductId());
            if ($categoryId !== $product->getCategory()) {
                return true;
            }

            $totalQtyInCategory += $inputItem->getQuantity();
            if ($totalQtyInCategory >= $requiredQty) {
                return true;
            }
        }

        return false;
    }

    public function apply(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): void
    {
        $productIdToDiscount = $this->findCheapestProductId($orderInput);

        /** @var ItemOutput $itemOutput */
        foreach ($orderOutput->getItems() as $itemOutput) {
            if ($itemOutput->getProductId() === $productIdToDiscount) {
                $discountValue = self::resolveDiscountedValue(
                    $itemOutput->getUnitPrice(),
                    $discount
                );

                $discountOutput = $this->discountOutputFactory->create($discount, $discountValue);
                $itemOutput->addDiscount($discountOutput);

                $itemOutput->setTotal($itemOutput->getTotal() - $discountValue);
                $orderOutput->setTotal($orderOutput->getTotal() - $discountValue);
            }
        }
    }

    private function findCheapestProductId(OrderInput $orderInput): string
    {
        $mappedPrices = [];
        /** @var ItemInput $inputItem */
        foreach ($orderInput->getItems() as $inputItem) {
            $mappedPrices[$inputItem->getProductId()] = $inputItem->getUnitPrice();
        }

        arsort($mappedPrices, SORT_ASC);

        return array_key_last($mappedPrices);
    }
}
