<?php

declare(strict_types=1);

namespace App\Tests\Discount\Processor;

use App\Discount\Entity\Discount;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Processor\DiscountedValueResolver;
use PHPUnit\Framework\TestCase;

class PercentOrFixedValueResolverTest extends TestCase
{
    use DiscountedValueResolver;

    /**
     * @dataProvider fixedCasesProvider
     */
    public function testFixed(float $expectedResult, float $referenceValue, int $fixedValue): void
    {
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CATEGORY,
            ApplyType::FIXED,
            $fixedValue,
            []
        );

        $result = self::resolveDiscountedValue($referenceValue, $discount);

        self::assertEquals($expectedResult, $result);
    }

    public function fixedCasesProvider(): \Generator
    {
        yield [
            'expectedResult' => 5.0,
            'referenceValue' => 10.0,
            'fixedValue' => 500
        ];

        yield [
            'expectedResult' => 4.0,
            'referenceValue' => 10.0,
            'fixedValue' => 600
        ];

        yield [
            'expectedResult' => 0.0,
            'referenceValue' => 10.0,
            'fixedValue' => 50000
        ];
    }

    /**
     * @dataProvider percentCasesProvider
     */
    public function testPercent(float $expectedResult, float $referenceValue, int $percentValue): void
    {
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CATEGORY,
            ApplyType::PERCENT,
            $percentValue,
            []
        );

        $result = self::resolveDiscountedValue($referenceValue, $discount);

        self::assertEquals($expectedResult, $result);
    }

    public function percentCasesProvider(): \Generator
    {
        yield [
            'expectedResult' => 2.0,
            'referenceValue' => 10.0,
            'fixedValue' => 20
        ];

        yield [
            'expectedResult' => 10.0,
            'referenceValue' => 10.0,
            'fixedValue' => 100
        ];

        yield [
            'expectedResult' => 0.0,
            'referenceValue' => 10.0,
            'fixedValue' => 0
        ];
    }
}
