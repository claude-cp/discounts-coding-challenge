<?php

declare(strict_types=1);

namespace App\Tests\Discount\Processor\Collection;

use App\Customer\Repository\CustomerRepository;
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
use App\Discount\Processor\Collection\OrderTotalProcessor;
use PHPUnit\Framework\TestCase;

class OrderTotalProcessorTest extends TestCase
{
    private OrderOutputTransformer $orderOutputTransformer;

    private OrderTotalProcessor $orderTotalProcessor;

    protected function setUp(): void
    {
        $this->orderOutputTransformer = new OrderOutputTransformer();

        $this->orderTotalProcessor = new OrderTotalProcessor(
            new DiscountOutputFactory(),
            new CustomerRepository(),
        );
    }

    public function testIsApplicableHappyPath(): void
    {
        $orderInput = (new OrderInput())->setCustomerId(1);
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ORDER_TOTAL_VALUE,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::THRESHOLD => 490]
        );

        $result = $this->orderTotalProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(true, $result);
    }

    public function testNotApplicableCustomerDoesNotMeetThreshold(): void
    {
        $orderInput = (new OrderInput())->setCustomerId(1);
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ORDER_TOTAL_VALUE,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::THRESHOLD => 500]
        );

        $result = $this->orderTotalProcessor->isApplicable($orderInput, $orderOutput, $discount);

        self::assertEquals(false, $result);
    }

    public function testNotApplicableCustomerNotFound(): void
    {
        $orderInput = (new OrderInput())->setCustomerId(10000000000);
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ORDER_TOTAL_VALUE,
            ApplyType::PERCENT,
            50,
            [DiscountConfig::THRESHOLD => 1]
        );

        $result = $this->orderTotalProcessor->isApplicable($orderInput, $orderOutput, $discount);

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
                        ->setUnitPrice(1000.0)
                        ->setTotal(1000.0)
                ]
            )
            ->setTotal(1000.0)
            ->setCustomerId(1);

        $orderOutput = $this->orderOutputTransformer->transform($orderInput);
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ORDER_TOTAL_VALUE,
            ApplyType::PERCENT,
            10,
            [DiscountConfig::THRESHOLD => 50]
        );

        self::assertEquals(1000.0, $orderOutput->getTotal());
        self::assertEquals(1000.0, $orderOutput->getTotalBeforeDiscounts());

        $this->orderTotalProcessor->apply($orderInput, $orderOutput, $discount);

        $expectedOrderOutput = (new OrderOutput())
            ->setId(1)
            ->setCustomerId(1)
            ->setItems(
                [
                    (new ItemOutput())
                        ->setProductId('A101')
                        ->setQuantity(1)
                        ->setUnitPrice(1000.0)
                        ->setTotal(1000.0)
                        ->setTotalBeforeDiscounts(1000.0)
                ])
            ->setTotalBeforeDiscounts(1000.0)
            ->setTotal(900.0)
            ->addDiscount((new DiscountOutput())
                ->setValue(100.0)
                ->setDiscountCode('code')
                ->setApplyType(ApplyType::PERCENT->value)
            );

        self::assertEquals($expectedOrderOutput, $orderOutput);
    }
}
