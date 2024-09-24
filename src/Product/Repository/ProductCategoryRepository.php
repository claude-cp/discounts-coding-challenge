<?php

declare(strict_types=1);

namespace App\Product\Repository;

use App\Product\Entity\ProductInterface;

/**
 * FAKE REPOSITORY
 */
class ProductCategoryRepository
{
    private const CATEGORY_DATA = ['1', '2'];

    public function findById(string $id): ?string
    {
        foreach (self::CATEGORY_DATA as $dbId) {
            if ($id === $dbId) {
                return $dbId;
            }
        }

        return null;
    }
}
