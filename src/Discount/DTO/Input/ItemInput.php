<?php

namespace App\Discount\DTO\Input;

use App\AppCommon\Model\InputModelInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ItemInput implements InputModelInterface
{
    /**
     * @Assert\NotBlank
     * @SerializedName("product-id")
     */
    private ?string $productId = null;

    /**
     * @Assert\NotBlank
     */
    private ?int $quantity = null;

    /**
     * @Assert\NotBlank
     * @SerializedName("unit-price")
     */
    private ?float $unitPrice = null;

    /**
     * @Assert\NotBlank
     */
    private ?float $total = null;

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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
