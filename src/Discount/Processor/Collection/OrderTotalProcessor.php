<?php

declare(strict_types=1);

namespace App\Discount\Processor\Collection;

use App\Customer\Entity\CustomerInterface;
use App\Customer\Repository\CustomerRepository;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\OrderOutput;
use App\Discount\Entity\DiscountConfig;
use App\Discount\Entity\DiscountInterface;
use App\Discount\Enum\DiscountType;
use App\Discount\Factory\DiscountOutputFactory;
use App\Discount\Processor\DiscountedValueResolver;

class OrderTotalProcessor extends AbstractDiscountProcessor
{
    use DiscountedValueResolver;

    public function __construct(
        protected DiscountOutputFactory $discountOutputFactory,
        private readonly CustomerRepository $customerRepository,
    ){
        parent::__construct($this->discountOutputFactory);
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::ORDER_TOTAL_VALUE;
    }

    public function isApplicable(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): bool
    {
        $discountConfig = $discount->getConfiguration();
        $ruleThreshold = $discountConfig[DiscountConfig::THRESHOLD];

        $customerFromOrder = $orderInput->getCustomerId();
        $customerFromDb = $this->customerRepository->findById($customerFromOrder);
        if (!$customerFromDb instanceof CustomerInterface) {
            return false;
        }

        if ($customerFromDb->getOrdersTotal() >= $ruleThreshold) {
            return true;
        }

        return false;
    }

    public function apply(OrderInput $orderInput, OrderOutput $orderOutput, DiscountInterface $discount): void
    {
        $currentOrderTotal = $orderOutput->getTotal();
        $discountedValue = self::resolveDiscountedValue($currentOrderTotal, $discount);

        $discountOutput = $this->discountOutputFactory->create($discount, $discountedValue);
        $orderOutput->setTotal($orderOutput->getTotal() - $discountedValue);

        $orderOutput->addDiscount($discountOutput);
    }
}
