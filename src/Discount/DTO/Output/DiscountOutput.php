<?php

namespace App\Discount\DTO\Output;

class DiscountOutput
{
    private ?string $discountCode;
    private ?string $applyType;
    private ?float $value;

    public function getDiscountCode(): ?string
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?string $discountCode): self
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    public function getApplyType(): ?string
    {
        return $this->applyType;
    }

    public function setApplyType(?string $applyType): self
    {
        $this->applyType = $applyType;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }
}
