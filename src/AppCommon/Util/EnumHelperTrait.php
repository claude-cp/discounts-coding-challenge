<?php

declare(strict_types=1);

namespace App\AppCommon\Util;

/**
 * Apart from our custom "::values()" method here,
 * some other most useful built-in functions for php 8.1^ Enum types are: ::cases(), ::from(), ::tryFrom()
 */
trait EnumHelperTrait
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
