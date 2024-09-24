<?php

declare(strict_types=1);

namespace App\Customer\Repository;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerInterface;

/**
 * FAKE REPOSITORY
 */
class CustomerRepository
{
    public function findById(int $id): ?CustomerInterface
    {
        foreach (self::CUSTOMER_DATA as $customerDatum) {
            if ($id === $customerDatum['id']) {
                return new Customer(
                    $customerDatum['id'],
                    $customerDatum['ordersTotal'],
                );
            }
        }

        return null;
    }

    private const CUSTOMER_DATA = [
        [
            "id" => 1,
            "ordersTotal" => 492.12,
        ],
        [
            "id" => 2,
            "ordersTotal" => 1505.95,
        ],
        [
            "id" => 3,
            "ordersTotal" => 0.00,
        ],
    ];
}
