<?php

declare(strict_types=1);

namespace App\Discount\Enum;

use App\AppCommon\Util\EnumHelperTrait;

/**
 * Note for assessors:
 *
 * A big initial architectural decision here could be to have decoupled ruleTypes and actionTypes for extreme modularity
 * We can discuss more.
 */
enum DiscountType: string
{
    use EnumHelperTrait;

    case ORDER_TOTAL_VALUE = 'order_total_value';

    case ITEM_CATEGORY = 'item_category';
    case ITEM_CHEAPEST = 'item_cheapest';
}
