<?php

declare(strict_types=1);

namespace App\Discount\Entity;

use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;
use App\Discount\Validator as Validator;

class Discount implements DiscountInterface
{
    private bool $enabled = true;

    public function __construct(
        private int               $id,          // unique in our fictional db
        private string            $code,        // unique in our fictional db
        private int               $priority,    // highest is first
        private string            $description,
        private DiscountType      $type,
        private ApplyType         $applyType,
        private int               $discountValue,
        private array             $configuration
    ){
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
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

    public function getType(): DiscountType
    {
        return $this->type;
    }

    public function setType(DiscountType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getApplyType(): ApplyType
    {
        return $this->applyType;
    }

    public function setApplyType(ApplyType $applyType): self
    {
        $this->applyType = $applyType;

        return $this;
    }

    public function getDiscountValue(): int
    {
        return $this->discountValue;
    }

    public function setDiscountValue(int $discountValue): self
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
