<?php

declare(strict_types=1);

namespace App\Product\Repository;

use App\Product\Entity\Product;
use App\Product\Entity\ProductInterface;

/**
 * FAKE REPOSITORY
 */
class ProductRepository
{
    public function findById(string $id): ?ProductInterface
    {
        foreach (self::PRODUCT_DATA as $productDatum) {
            if ($id === $productDatum['id']) {
                return new Product(
                    $productDatum['id'],
                    $productDatum['description'],
                    $productDatum['category'],
                    $productDatum['price'],
                );
            }
        }

        return null;
    }

    private const PRODUCT_DATA = [
        [
            "id" => "A101",
            "description" => "Screwdriver",
            "category" => "1",
            "price" => 9.75,
        ],
        [
            "id" => "A102",
            "description" => "Electric screwdriver",
            "category" => "1",
            "price" => 49.50,
        ],
        [
            "id" => "B101",
            "description" => "Basic on-off switch",
            "category" => "2",
            "price" => 4.99,
        ],
        [
            "id" => "B102",
            "description" => "Press button",
            "category" => "2",
            "price" => 4.99,
        ],
        [
            "id" => "B103",
            "description" => "Switch with motion detector",
            "category" => "2",
            "price" => 12.95,
        ],
    ];
}
