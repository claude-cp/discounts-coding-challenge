<?php

declare(strict_types=1);

namespace App\Tests\Discount\Manager;

use App\Discount\DataTransformer\OrderOutputTransformer;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Manager\OrderDiscountsManager;
use App\Discount\Processor\DiscountApplicator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderDiscountManagerTest extends TestCase
{
    /**
     * @var MockObject&OrderOutputTransformer
     */
    private MockObject $orderOutputTransformer;

    /**
     * @var MockObject&DiscountApplicator
     */
    private MockObject $discountApplicator;

    private OrderDiscountsManager $manager;

    protected function setUp(): void
    {
        $this->orderOutputTransformer = $this->createMock(OrderOutputTransformer::class);
        $this->discountApplicator = $this->createMock(DiscountApplicator::class);

        $this->manager = new OrderDiscountsManager(
            $this->orderOutputTransformer,
            $this->discountApplicator,
        );
    }

    public function testItAppliesDiscounts(): void
    {
        $orderInput = (new OrderInput())->setId(234523452435);
        $orderOutput = (new OrderOutput())->setId(234523452435);

        $this->orderOutputTransformer
            ->expects(self::once())
            ->method('transform')
            ->with($orderInput)
            ->willReturn($orderOutput);

        $this->discountApplicator
            ->expects(self::once())
            ->method('applyDiscounts')
            ->with($orderInput, $orderOutput);

        $result = $this->manager->processOrderAndApplyDiscounts($orderInput);

        self::assertEquals($orderOutput, $result);
    }
}
