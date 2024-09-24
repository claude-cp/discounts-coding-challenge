<?php

declare(strict_types=1);

namespace App\Product\Entity;

interface ProductInterface
{
    public function getId(): string;

    public function getDescription(): string;
    public function setDescription(string $description): self;

    public function getCategory(): string;
    public function setCategory(string $category): self;

    public function getPrice(): float;
    public function setPrice(float $price): self;

    public function getPriceAsInt(): int;
}
