<?php

declare(strict_types=1);

namespace App\Discount\Validator\Discount;

use Symfony\Component\Validator\Constraint;

class ApplyTypeConstraint extends Constraint
{
    public $badDiscountPercentValue = 'For "percent" discounts, "discountValue" needs to be an integer between 1 and 100';
    public $badDiscountFixedValue = 'For "fixed" discounts, "discountValue" needs to be integer 1 or greater';
}
