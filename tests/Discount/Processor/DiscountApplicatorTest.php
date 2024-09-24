<?php

declare(strict_types=1);

namespace App\Tests\Discount\Processor;

use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\Discount;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Processor\Collection\DiscountProcessorInterface;
use App\Discount\Processor\DiscountApplicator;
use App\Discount\Repository\DiscountRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DiscountApplicatorTest extends TestCase
{

    /**
     * @var MockObject&DiscountProcessorInterface
     */
    private MockObject $dummyDiscountProcessorOne;

    /**
     * @var MockObject&DiscountProcessorInterface
     */
    private MockObject $dummyDiscountProcessorTwo;

    /**
     * @var MockObject&DiscountProcessorInterface
     */
    private MockObject $dummyDiscountProcessorThree;

    /**
     * @var MockObject&DiscountRepository
     */
    private MockObject $discountRepository;

    private DiscountApplicator $discountApplicator;

    protected function setUp(): void
    {

        $this->dummyDiscountProcessorOne = $this->createMock(DiscountProcessorInterface::class);
        $this->dummyDiscountProcessorTwo = $this->createMock(DiscountProcessorInterface::class);
        $this->dummyDiscountProcessorThree = $this->createMock(DiscountProcessorInterface::class);
        $this->discountRepository = $this->createMock(DiscountRepository::class);

        $this->discountApplicator =
            new DiscountApplicator([
                    $this->dummyDiscountProcessorOne,
                    $this->dummyDiscountProcessorTwo,
                    $this->dummyDiscountProcessorThree,
                ],
                $this->discountRepository,
        );
    }

    public function testItIteratesAndAppliesDiscounts(): void
    {
        $orderInput = (new OrderInput())->setId(99945634);
        $orderOutput = (new OrderOutput())->setId(99945634);

        $this->discountRepository
            ->expects(self::once())
            ->method('getAllSorted')
            ->willReturn(self::getDummyDiscounts());

        $this->dummyDiscountProcessorOne->expects(self::once())->method('getDiscountType')->willReturn(DiscountType::ITEM_CATEGORY);
        $this->dummyDiscountProcessorOne->expects(self::once())->method('isApplicable')->willReturn(true);
        $this->dummyDiscountProcessorOne->expects(self::once())->method('apply')->with($orderInput, $orderOutput, current(self::getDummyDiscounts()));

        $this->dummyDiscountProcessorTwo->expects(self::once())->method('getDiscountType')->willReturn(DiscountType::ITEM_CATEGORY);
        $this->dummyDiscountProcessorTwo->expects(self::once())->method('isApplicable')->willReturn(false);
        $this->dummyDiscountProcessorTwo->expects(self::never())->method('apply');

        $this->dummyDiscountProcessorThree->expects(self::once())->method('getDiscountType')->willReturn(DiscountType::ITEM_CHEAPEST);
        $this->dummyDiscountProcessorThree->expects(self::never())->method('isApplicable');
        $this->dummyDiscountProcessorThree->expects(self::never())->method('apply');

        $this->discountApplicator->applyDiscounts($orderInput, $orderOutput);
    }

    private static function getDummyDiscounts(): array
    {
        return [
            new Discount(
                1,
                "code",
                3,
                "description",
                DiscountType::ITEM_CATEGORY,
                ApplyType::PERCENT,
                50,
                []
            )
        ];
    }
}
