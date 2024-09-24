<?php

declare(strict_types=1);

namespace App\Tests\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use App\Discount\Validator\DiscountConfiguration\RequiredQtyConstraint;
use App\Discount\Validator\DiscountConfiguration\RequiredQtyConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class RequiredQtyConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): RequiredQtyConstraintValidator
    {
        return new RequiredQtyConstraintValidator();
    }

    public function testHappyPathDiscountValues(): void
    {
        $discountConfiguration = [DiscountConfig::REQUIRED_QTY => 5];

        $this->validator->validate($discountConfiguration, new RequiredQtyConstraint());

        $this->assertNoViolation();
    }

    public function testWhenRequiredQtyKeyIsMissing(): void
    {
        $discountConfiguration = ['bla bla' => 1324312];

        $constraint = new RequiredQtyConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->requiredQtyUndefined)->assertRaised();
    }

    public function testWhenRequiredQtyKeyIsNotInteger(): void
    {
        $discountConfiguration = [DiscountConfig::REQUIRED_QTY => 'dfghew'];

        $constraint = new RequiredQtyConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->requiredQtyInteger)->assertRaised();
    }

    public function testWhenRequiredQtyIsLowerThanOne(): void
    {
        $discountConfiguration = [DiscountConfig::REQUIRED_QTY => 0];

        $constraint = new RequiredQtyConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->requiredQtyInteger)->assertRaised();
    }
}
