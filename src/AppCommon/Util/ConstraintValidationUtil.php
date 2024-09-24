<?php

declare(strict_types=1);

namespace App\AppCommon\Util;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintValidationUtil
{
    public static function toString(ConstraintViolationListInterface $violations): string
    {
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $invalidValue = $violation->getInvalidValue();
            if (\is_object($invalidValue)) {
                $invalidValue = \get_class($invalidValue);
            }
            $errors[] = sprintf(
                '%s: %s (%s)',
                $violation->getPropertyPath(),
                $violation->getMessage(),
                $invalidValue
            );
        }

        return implode(', ', $errors);
    }
}
