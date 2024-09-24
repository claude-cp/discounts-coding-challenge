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
use App\Product\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;

class ItemCategoryProcessorTest extends TestCase
{
    private OrderOutputTransformer $orderOutputTransformer;

    private ItemCategoryProcessor $itemCategoryProcessor;

    protected function setUp(): void
    {
        $this->orderOutputTransformer = new OrderOutputTransformer();

        $this->itemCategoryProcessor = new ItemCategoryProcessor(
            new DiscountOutputFactory(),
            new ProductRepository(),
        );
    }

    public function testIsApplicableHappyPath(): void
    {
        $orderInput = (new OrderInput())->setItems(
            [
                (new ItemInput())
                    ->setProductId('A102')
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

        $result = $this->itemCategoryProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(true, $result);
    }

    public function testNotApplicableBecauseQtyNotMet(): void
    {
        $orderInput = (new OrderInput())->setItems(
            [
                (new ItemInput())
                    ->setProductId('A102')
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
            [DiscountConfig::REQUIRED_QTY => 7, DiscountConfig::CATEGORY_ID => '1']
        );

        $result = $this->itemCategoryProcessor->isApplicable($orderInput, $orderOutput, $discount);

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

        $result = $this->itemCategoryProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(false, $result);
    }

    public function testItAppliesDiscount(): void
    {
        $orderInput = (new OrderInput())
            ->setId(1)
            ->setItems(
                [
                    (new ItemInput())
                        ->setProductId('A102')
                        ->setQuantity(5)
                        ->setUnitPrice(10.0)
                        ->setTotal(50.0)
                ])
            ->setTotal(50.0)
            ->setCustomerId(1);

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

        $this->itemCategoryProcessor->apply($orderInput, $orderOutput, $discount);

        $expectedOrderOutput = (new OrderOutput())->setId(1)
            ->setItems(
                [
                    (new ItemOutput())
                        ->setProductId('A102')
                        ->setQuantity(5)
                        ->setUnitPrice(10.0)
                        ->setTotal(45.0)
                        ->setTotalBeforeDiscounts(50.0)
                        ->addDiscount((new DiscountOutput())
                            ->setValue(5.0)
                            ->setDiscountCode('code')
                            ->setApplyType(ApplyType::PERCENT->value)
                        )
                ])
            ->setTotalBeforeDiscounts(50.0)
            ->setTotal(45.0)
            ->setCustomerId(1);

        self::assertEquals($expectedOrderOutput, $orderOutput);
    }
}
