<?php

declare(strict_types=1);

namespace App\AppCommon\Exception;

use App\AppCommon\Util\ConstraintValidationUtil;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationException extends \RuntimeException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violationList;

    public function __construct(
        ConstraintViolationListInterface $violationList,
        string $message = 'Validation error',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->violationList = $violationList;

        $this->message = $this->getViolationMessage();
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }

    public function getViolationMessage(): string
    {
        return sprintf('%s: %s', $this->getMessage(), ConstraintValidationUtil::toString($this->violationList));
    }
}
