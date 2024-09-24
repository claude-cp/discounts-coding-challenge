<?php

declare(strict_types=1);

namespace App\Discount\Factory;

use App\Discount\DTO\Output\DiscountOutput;
use App\Discount\Entity\DiscountInterface;

class DiscountOutputFactory
{
    public function create(DiscountInterface $discount, float $discountValue): DiscountOutput
    {
        return (new DiscountOutput())
            ->setDiscountCode($discount->getCode())
            ->setValue($discountValue)
            ->setApplyType($discount->getApplyType()->value);
    }
}
