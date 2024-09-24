<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use Symfony\Component\Validator\Constraint;

class OrderTotalValueConstraint extends Constraint
{
    public $missingMinimumOrderTotalValue = 'You need to specify the "threshold" (minimum order total value) in the configuration';
    public $orderTotalValueNotPositiveInteger = 'The "threshold" value needs to be 0 or positive integer';
}
