<?php

declare(strict_types=1);

namespace App\Product\Entity;

class Product implements ProductInterface
{
    /**
     * IRL, this would be an entity with actual ORM mappings.
     */
    public function __construct(
        private string $id,
        private string $description,
        private string $category,
        private float $price,
    ){
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceAsInt(): int
    {
        return (int) ($this->price * 100);
    }
}
