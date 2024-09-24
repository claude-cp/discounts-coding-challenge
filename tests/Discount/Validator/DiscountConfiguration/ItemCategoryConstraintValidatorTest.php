<?php

declare(strict_types=1);

namespace App\Tests\Discount\Validator\DiscountConfiguration;

use App\Discount\Entity\DiscountConfig;
use App\Discount\Validator\DiscountConfiguration\ItemCategoryConstraint;
use App\Discount\Validator\DiscountConfiguration\ItemCategoryConstraintValidator;
use App\Product\Repository\ProductCategoryRepository;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ItemCategoryConstraintValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ItemCategoryConstraintValidator
    {
        return new ItemCategoryConstraintValidator(new ProductCategoryRepository());
    }

    public function testHappyPathDiscountValues(): void
    {
        $discountConfiguration = [DiscountConfig::CATEGORY_ID => '1'];

        $this->validator->validate($discountConfiguration, new ItemCategoryConstraint());

        $this->assertNoViolation();
    }

    public function testWhenCategoryIdKeyIsMissing(): void
    {
        $discountConfiguration = ['bla bla' => 1324312];

        $constraint = new ItemCategoryConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->categoryIdUndefined)->assertRaised();
    }

    public function testWhenCategoryIdKeyIsNotAString(): void
    {
        $discountConfiguration = [DiscountConfig::CATEGORY_ID => 1324312];

        $constraint = new ItemCategoryConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->categoryIdNotString)->assertRaised();
    }

    public function testWhenCategoryIdNotFoundInDb(): void
    {
        $discountConfiguration = [DiscountConfig::CATEGORY_ID => '1324312'];

        $constraint = new ItemCategoryConstraint();
        $this->validator->validate($discountConfiguration, $constraint);

        $this->buildViolation($constraint->categoryIdNotFound)->assertRaised();
    }
}
