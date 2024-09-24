<?php

namespace App\Discount\DTO\Output;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ItemOutput
{
    use DiscountOutputTrait;

    /**
     * @SerializedName("product-id")
     */
    private ?string $productId = null;

    private ?int $quantity = null;

    /**
     * @SerializedName("unit-price")
     */
    private ?float $unitPrice = null;

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }
}
