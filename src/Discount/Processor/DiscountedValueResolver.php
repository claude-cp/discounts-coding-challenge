<?php

declare(strict_types=1);

namespace App\Discount\Processor;

use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\ApplyType;

trait DiscountedValueResolver
{
    private static function resolveDiscountedValue(float $referenceValue, DiscountInterface $discount): float
    {
        $applyType = $discount->getApplyType();

        switch (true) {
            case ApplyType::PERCENT === $applyType:
                return round($referenceValue * $discount->getDiscountValue() / 100, 2);
            case ApplyType::FIXED === $applyType:
                return round((float) max(0.0, $referenceValue - $discount->getDiscountValue() / 100), 2);
            default:
                throw new \LogicException(sprintf('Only percent and fixed are supported for now'));
        }
    }
}
