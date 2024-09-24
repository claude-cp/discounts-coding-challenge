<?php

namespace App\Discount\DTO\Output;

use Symfony\Component\Serializer\Annotation\SerializedName;

class OrderOutput
{
    use DiscountOutputTrait;

    private ?int $id = null;

    /**
     * @SerializedName("customer-id")
     */
    private ?int $customerId = null;

    /**
     * @var ItemOutput[] $items
     */
    private array $items = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}
