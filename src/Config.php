<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

class Config
{
    public static function create(
        int $defaultTimeout,
        Logger $logger,
        int $commandPreFetch,
        int $sessionPreFetch,
        int $pruneInterval
    ): self {
        return new self(
            $defaultTimeout,
            $logger,
            $commandPreFetch,
            $sessionPreFetch,
            $pruneInterval
        );
    }

    private function __construct(
        int $defaultTimeout,
        Logger $logger,
        int $commandPreFetch,
        int $sessionPreFetch,
        int $pruneInterval
    ) {
        $this->timeout = $timeout;
        $this->traceId = $traceId;
    }

    private $defaultTimeout;
    private $logger;
    private $commandPreFetch;
    private $sessionPreFetch;
    private $pruneInterval;
}
