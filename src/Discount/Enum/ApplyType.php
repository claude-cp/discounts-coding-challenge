<?php

declare(strict_types=1);

namespace App\Discount\Enum;

use App\AppCommon\Util\EnumHelperTrait;

enum ApplyType: string
{
    use EnumHelperTrait;

    case FIXED = 'fixed';
    case PERCENT = 'percent';
}
