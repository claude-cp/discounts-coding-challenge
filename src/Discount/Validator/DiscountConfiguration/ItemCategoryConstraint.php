<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use Symfony\Component\Validator\Constraint;

class ItemCategoryConstraint extends Constraint
{
    public $categoryIdUndefined = 'Configuration needs to define "categoryId" key';
    public $categoryIdNotString = '"categoryId" needs to be a string';
    public $categoryIdNotFound = '"categoryId" not found in database';
}
