<?php

declare(strict_types=1);

namespace App\Discount\Processor\Collection;

use App\Discount\Factory\DiscountOutputFactory;

abstract class AbstractDiscountProcessor implements DiscountProcessorInterface
{
    public function __construct(protected DiscountOutputFactory $discountOutputFactory)
    {
    }
}
