<?php

declare(strict_types=1);

namespace App\Tests\Discount\Validator\Discount;

use App\Discount\Entity\Discount;
use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Validator\Discount\ApplyTypeConstraint;
use App\Discount\Validator\Discount\ApplyTypeConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ApplyTypeConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ApplyTypeConstraintValidator
    {
        return new ApplyTypeConstraintValidator();
    }

    /**
     * @dataProvider happyPathProvider
     */
    public function testHappyPathDiscountValues(ApplyType $applyType, int $discountValue): void
    {
        $discount = new Discount(
            1,
            "code",
            3,
            "description",
            DiscountType::ITEM_CATEGORY,
            $applyType,
            $discountValue,
            []
        );

        $this->validator->validate($discount, new ApplyTypeConstraint());

        $this->assertNoViolation();
    }

    public function happyPathProvider(): \Generator
    {
        yield [ApplyType::PERCENT, 1];
        yield [ApplyType::PERCENT, 50];
        yield [ApplyType::PERCENT, 100];

        yield [ApplyType::FIXED, 1];
        yield [ApplyType::FIXED, 100];
        yield [ApplyType::FIXED, 1000000];
    }

    /**
     * @dataProvider badCasesProvider
     */
    public function testBadDiscountValuesForApplyType(
        string $expectedConstraintMessage,
        ApplyType $applyType,
        int $discountValue
    ): void {
        $discount = new Discount(
            1,
            "code",
            3,"description",
            DiscountType::ITEM_CATEGORY,
            $applyType,
            $discountValue,
            []
        );

        $constraint = new ApplyTypeConstraint();
        $this->validator->validate($discount, $constraint);

        $this->buildViolation($expectedConstraintMessage)->assertRaised();
    }

    public function badCasesProvider(): \Generator
    {
        $constraint = new ApplyTypeConstraint();

        yield [$constraint->badDiscountPercentValue, ApplyType::PERCENT, 0];
        yield [$constraint->badDiscountPercentValue, ApplyType::PERCENT, -10];
        yield [$constraint->badDiscountPercentValue, ApplyType::PERCENT, 101];

        yield [$constraint->badDiscountFixedValue, ApplyType::FIXED, -30];
        yield [$constraint->badDiscountFixedValue, ApplyType::FIXED, 0];
    }
}
