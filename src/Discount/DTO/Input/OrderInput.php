<?php

namespace App\Discount\DTO\Input;

use App\AppCommon\Model\InputModelInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class OrderInput implements InputModelInterface
{
    /**
     * @Assert\NotBlank
     */
    private ?int $id = null;

    /**
     * @Assert\NotBlank
     * @SerializedName("customer-id")
     */
    private ?int $customerId = null;

    /**
     * @Assert\NotBlank
     */
    private ?float $total = null;

    /**
     * @var ItemInput[] $items
     *
     * @Assert\NotBlank
     * @Assert\Valid
     */
    private ?array $items = [];

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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(ItemInput $itemInput): self
    {
        $this->items[] = $itemInput;

        return $this;
    }
}
