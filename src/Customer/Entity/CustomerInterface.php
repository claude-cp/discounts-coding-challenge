<?php

declare(strict_types=1);

namespace App\Customer\Entity;

interface CustomerInterface
{
    public function getId(): int;

    public function getOrdersTotal(): float;
    public function setOrdersTotal(float $ordersTotal): self;
}
