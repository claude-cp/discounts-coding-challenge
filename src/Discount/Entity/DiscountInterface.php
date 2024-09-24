<?php

declare(strict_types=1);

namespace App\Discount\Entity;

use App\Discount\Enum\ApplyType;
use App\Discount\Enum\DiscountType;

interface DiscountInterface
{
    public function getId(): int;

    public function isEnabled(): bool;
    public function setEnabled(bool $enabled): self;

    public function getCode(): string;
    public function setCode(string $code): self;

    public function getPriority(): int;
    public function setPriority(int $priority): self;

    public function getDescription(): string;
    public function setDescription(string $description): self;

    public function getType(): DiscountType;
    public function setType(DiscountType $type): self;

    public function getApplyType(): ApplyType;
    public function setApplyType(ApplyType $applyType): self;

    public function getDiscountValue(): int;
    public function setDiscountValue(int $discountValue): self;

    public function getConfiguration(): array;
    public function setConfiguration(array $configuration): self;
}
