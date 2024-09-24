<?php

declare(strict_types=1);

namespace App\AppCommon\Logging;

use Psr\Log\LoggerAwareTrait as BaseLoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait LoggerAwareTrait
{
    use BaseLoggerAwareTrait;

    public function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    public function getExceptionContext(\Throwable $throwable, ?array $additionalContext = [], ?bool $showExceptionMessage = true): array
    {
        $context = [];

        if ($showExceptionMessage) {
            $context['exceptionMessage'] = $throwable->getMessage();
        }

        return \array_merge(
            $context,
            [
                'exceptionClass' => \get_class($throwable),
                'exceptionTrace' => $throwable->getTraceAsString(),
            ],
            $additionalContext
        );
    }
}
