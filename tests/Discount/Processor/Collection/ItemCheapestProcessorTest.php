<?php

declare(strict_types=1);

namespace App\Tests\Discount\Processor\Collection;

use App\Discount\DataTransformer\OrderOutputTransformer;
use App\Discount\DTO\Input\ItemInput;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\DiscountOutput;
use App\Discount\DTO\Output\ItemOutput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\Discount;
use App\Discount\Entity\DiscountConfig;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Factory\DiscountOutputFactory;
use App\Discount\Processor\Collection\ItemCategoryProcessor;
use App\Discount\Processor\Collection\ItemCheapestProcessor;
use App\Product\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class ItemCheapestProcessorTest extends TestCase
{
    private OrderOutputTransformer $orderOutputTransformer;

    private ItemCheapestProcessor $itemCheapestProcessor;

    protected function setUp(): void
    {
        $this->orderOutputTransformer = new OrderOutputTransformer();

        $this->itemCheapestProcessor = new ItemCheapestProcessor(
            new DiscountOutputFactory(),
            new ProductRepository(),
        );
    }

    public function testIsApplicableHappyPath(): void
    {
        $orderInput = (new OrderInput())->setItems(
            [
                (new ItemInput())
                    ->setProductId('A101')
                    ->setQuantity(1),
                (new ItemInput())
                    ->setProductId('A102')
                    ->setQuantity(2)
            ]
        );
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);

        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CHEAPEST,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::REQUIRED_QTY => 3, DiscountConfig::CATEGORY_ID => '1']
        );

        $result = $this->itemCheapestProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(true, $result);
    }

    public function testNotApplicableBecauseQtyNotMet(): void
    {
        $orderInput = (new OrderInput())->setItems(
            [
                (new ItemInput())
                    ->setProductId('A102')
                    ->setQuantity(5)
                    ->setUnitPrice(5.5),
                (new ItemInput())
                    ->setProductId('B101')
                    ->setQuantity(10)
                    ->setUnitPrice(2.9)

            ]
        );
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);

        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CATEGORY,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::REQUIRED_QTY => 6, DiscountConfig::CATEGORY_ID => '1']
        );

        $result = $this->itemCheapestProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(false, $result);
    }

    public function testNotApplicableBecauseNoItemsFromCategory(): void
    {
        $orderInput = (new OrderInput())->setItems(
            [
                (new ItemInput())
                    ->setProductId('B102')
                    ->setQuantity(5)
            ]
        );
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);

        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CATEGORY,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::REQUIRED_QTY => 3, DiscountConfig::CATEGORY_ID => '1']
        );

        $result = $this->itemCheapestProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(false, $result);
    }

    public function testItAppliesDiscount(): void
    {
        $orderInput = (new OrderInput())
            ->setId(1)
            ->setItems(
                [
                    (new ItemInput())
                        ->setProductId('A101')
                        ->setQuantity(1)
                        ->setUnitPrice(3.0)
                        ->setTotal(3.0),
                    (new ItemInput())
                        ->setProductId('A102')
                        ->setQuantity(1)
                        ->setUnitPrice(5.5)
                        ->setTotal(27.5),
                    (new ItemInput())
                        ->setProductId('B101')
                        ->setQuantity(1)
                        ->setUnitPrice(2.9)
                        ->setTotal(2.9),
                    ]
            )
            ->setTotal(33.4)
            ->setCustomerId(1);

        $orderOutput = $this->orderOutputTransformer->transform($orderInput);

        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CHEAPEST,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::REQUIRED_QTY => 2, DiscountConfig::CATEGORY_ID => '1']
        );

        $this->itemCheapestProcessor->apply($orderInput, $orderOutput, $discount);

        $expectedOrderOutput = (new OrderOutput())->setId(1)
            ->setItems(
                [
                    (new ItemOutput())
                        ->setProductId('A101')
                        ->setQuantity(1)
                        ->setUnitPrice(3.0)
                        ->setTotal(3.0)
                        ->setTotalBeforeDiscounts(3.0),
                    (new ItemOutput())
                        ->setProductId('A102')
                        ->setQuantity(1)
                        ->setUnitPrice(5.5)
                        ->setTotal(27.5)
                        ->setTotalBeforeDiscounts(27.5),
                    (new ItemOutput())
                        ->setProductId('B101')
                        ->setQuantity(1)
                        ->setUnitPrice(2.9)
                        ->setTotal(1.45)
                        ->setTotalBeforeDiscounts(2.9)
                        ->addDiscount((new DiscountOutput())
                            ->setValue(1.45)
                            ->setDiscountCode('code')
                            ->setApplyType(ApplyType::PERCENT->value)
                        )

                ])
            ->setTotalBeforeDiscounts(33.4)
            ->setTotal(31.95)
            ->setCustomerId(1);

        self::assertEquals($expectedOrderOutput, $orderOutput);
    }
}
