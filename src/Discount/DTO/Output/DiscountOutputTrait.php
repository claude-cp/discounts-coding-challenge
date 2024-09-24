<?php

declare(strict_types=1);

namespace App\Discount\DTO\Output;

trait DiscountOutputTrait
{
    /**
     * @var DiscountOutput[]
     */
    private array $discounts = [];

    private ?float $totalBeforeDiscounts = null;

    private ?float $total = null;

    public function getTotalBeforeDiscounts(): ?float
    {
        return $this->totalBeforeDiscounts;
    }

    public function setTotalBeforeDiscounts(?float $totalBeforeDiscounts): self
    {
        $this->totalBeforeDiscounts = max($totalBeforeDiscounts, 0.0);

        return $this;
    }

    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function addDiscount(DiscountOutput $discountOutput): self
    {
        $this->discounts[] = $discountOutput;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = max($total, 0.0);

        return $this;
    }
}
