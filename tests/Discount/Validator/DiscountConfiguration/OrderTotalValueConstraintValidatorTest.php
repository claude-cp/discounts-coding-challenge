<?php

declare(strict_types=1);

namespace App\Tests\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use App\Discount\Validator\DiscountConfiguration\OrderTotalValueConstraintValidator;
use App\Discount\Validator\DiscountConfiguration\OrderTotalValueConstraint;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class OrderTotalValueConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): OrderTotalValueConstraintValidator
    {
        return new OrderTotalValueConstraintValidator();
    }

    public function testHappyPathDiscountValues(): void
    {
        $discountConfiguration = [DiscountConfig::THRESHOLD => 5];

        $this->validator->validate($discountConfiguration, new OrderTotalValueConstraint());

        $this->assertNoViolation();
    }

    public function testWhenThresholdKeyIsMissing(): void
    {
        $discountConfiguration = ['bla bla' => 1324312];

        $constraint = new OrderTotalValueConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->missingMinimumOrderTotalValue)->assertRaised();
    }

    public function testWhenThresholdKeyIsNotInteger(): void
    {
        $discountConfiguration = [DiscountConfig::THRESHOLD => 'dfghew'];

        $constraint = new OrderTotalValueConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->orderTotalValueNotPositiveInteger)->assertRaised();
    }

    public function testWhenThresholdIsLowerThanZero(): void
    {
        $discountConfiguration = [DiscountConfig::THRESHOLD => -1];

        $constraint = new OrderTotalValueConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->orderTotalValueNotPositiveInteger)->assertRaised();
    }
}
