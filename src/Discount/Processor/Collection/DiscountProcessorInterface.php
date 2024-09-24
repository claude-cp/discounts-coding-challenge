<?php

declare(strict_types=1);

namespace App\Discount\Processor\Collection;

use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\DiscountType;

interface DiscountProcessorInterface
{
    public function getDiscountType(): DiscountType;

    public function isApplicable(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): bool;

    public function apply(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): void;

}
