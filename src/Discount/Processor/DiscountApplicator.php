<?php

declare(strict_types=1);

namespace App\Discount\Processor;

use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Processor\Collection\DiscountProcessorInterface;
use App\Discount\Repository\DiscountRepository;

class DiscountApplicator
{
    public function __construct(
        private readonly iterable $discountProcessors,
        private readonly DiscountRepository $discountRepository,
    ) {
    }

    public function applyDiscounts(OrderInput $orderInput, OrderOutput $orderOutput): void
    {
        $discounts = $this->discountRepository->getAllSorted();

        /** @var DiscountInterface $discount */
        foreach ($discounts as $discount) {
            /** @var DiscountProcessorInterface $discountProcessor */
            foreach ($this->discountProcessors as $discountProcessor) {
                $processorFound = $discountProcessor->getDiscountType() === $discount->getType();
                if (!$processorFound) {
                    continue;
                }

                try {
                    $isApplicable = $discountProcessor->isApplicable($orderInput, $orderOutput, $discount);

                    if ($isApplicable) {
                        $discountProcessor->apply($orderInput, $orderOutput, $discount);
                    }
                } catch (\Exception) {
                    // discuss reconciliation mechanisms with "IRL" team.
                }
            }
        }

    }
}
