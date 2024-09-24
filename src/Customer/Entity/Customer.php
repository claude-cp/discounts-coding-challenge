<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Discount\Validator as Validator;

class Customer implements CustomerInterface
{
    public function __construct(
        private int $id,
        private float $ordersTotal = 0.0,
    ){
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrdersTotal(): float
    {
        return $this->ordersTotal;
    }

    public function setOrdersTotal(float $ordersTotal): self
    {
        $this->ordersTotal = $ordersTotal;

        return $this;
    }
}
