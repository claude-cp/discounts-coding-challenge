<?php

declare(strict_types=1);

namespace App\Discount\Validator\DiscountConfiguration;

use Symfony\Component\Validator\Constraint;

class RequiredQtyConstraint extends Constraint
{
    public $requiredQtyUndefined = 'Configuration needs to define "requiredQty" key';
    public $requiredQtyInteger = '"requiredQty" must be a positive integer greater than zero';
}
