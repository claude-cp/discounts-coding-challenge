<?php

declare(strict_types=1);

namespace App\Discount\Manager;

use App\Discount\DataTransformer\OrderOutputTransformer;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Processor\DiscountApplicator;

class OrderDiscountsManager
{
    public function  __construct(
        private readonly OrderOutputTransformer $orderOutputTransformer,
        private readonly DiscountApplicator     $discountApplicator,
    ){
    }

    public function processOrderAndApplyDiscounts(OrderInput $orderInput): OrderOutput
    {
        $orderOutput = $this->orderOutputTransformer->transform($orderInput);

        $this->discountApplicator->applyDiscounts($orderInput, $orderOutput);

        return $orderOutput;
    }
}
