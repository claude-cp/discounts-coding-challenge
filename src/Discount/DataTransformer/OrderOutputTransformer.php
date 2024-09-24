<?php

declare(strict_types=1);

namespace App\Discount\DataTransformer;

use App\Discount\DTO\Input\ItemInput;
use App\Discount\DTO\Input\OrderInput;
use App\Discount\DTO\Output\ItemOutput;
use App\Discount\DTO\Output\OrderOutput;

class OrderOutputTransformer
{
    public function transform(OrderInput $orderInput): OrderOutput
    {
        $orderOutput = new OrderOutput();
        $outputItems = [];

        $orderOutput
            ->setId($orderInput->getId())
            ->setTotal($orderInput->getTotal())
            ->setTotalBeforeDiscounts($orderInput->getTotal())
            ->setCustomerId($orderInput->getCustomerId());

        /** @var ItemInput $inputItem */
        foreach ($orderInput->getItems() as $inputItem) {
            $outputItems[] = (new ItemOutput())
                ->setQuantity($inputItem->getQuantity())
                ->setTotal($inputItem->getTotal())
                ->setTotalBeforeDiscounts($inputItem->getTotal())
                ->setProductId($inputItem->getProductId())
                ->setUnitPrice($inputItem->getUnitPrice());
        }

        $orderOutput->setItems($outputItems);

        return $orderOutput;
    }
}
