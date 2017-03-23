<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Async;

use Psr\Log\LoggerInterface;

final class Config
{
    /**
     * Create new configuration.
     *
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param float           $pruneInterval  How often session info is purged in seconds.
     * @param int             $commandWorkers Maximum accepted command requests.
     * @param int             $sessionWorkers Maximum allowed command responses.
     */
    public static function create(
        LoggerInterface $logger,
        float $defaultTimeout = 5.0,
        float $pruneInterval = 180.0,
        int $commandWorkers = 0,
        int $sessionWorkers = 0
    ): self {
        return new self(
            $logger,
            $defaultTimeout,
            $pruneInterval,
            $commandWorkers,
            $sessionWorkers
        );
    }

    /**
     * @return LoggerInterface Defines a target for all the logs.
     */
    public function logger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return float The default timeout in seconds.
     */
    public function defaultTimeout(): float
    {
        return $this->defaultTimeout;
    }

    /**
     * @return float How often session info is purged in seconds.
     */
    public function pruneInterval(): float
    {
        return $this->pruneInterval;
    }

    /**
     * @return int Maximum accepted command requests.
     */
    public function commandWorkers(): int
    {
        return $this->commandWorkers;
    }

    /**
     * @return int Maximum allowed command responses.
     */
    public function sessionWorkers(): int
    {
        return $this->sessionWorkers;
    }

    /**
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param float           $pruneInterval  How often session info is purged in seconds.
     * @param int             $commandWorkers Maximum accepted command requests.
     * @param int             $sessionWorkers Maximum allowed command responses.
     */
    private function __construct(
        LoggerInterface $logger,
        float $defaultTimeout,
        float $pruneInterval,
        int $commandWorkers,
        int $sessionWorkers
    ) {
        $this->logger = $logger;
        $this->defaultTimeout = $defaultTimeout;
        $this->pruneInterval = $pruneInterval;
        $this->commandWorkers = $commandWorkers;
        $this->sessionWorkers = $sessionWorkers;
    }

    /**
     * @var LoggerInterface Defines a target for all the logs.
     */
    private $logger;

    /**
     * DefaultTimeout specifies the maximum amount of time to wait for a call to
     * return. It is used if the context passed to {@see Session::call()} does not
     * already have a deadline.
     *
     * @var float The default timeout in seconds.
     */
    private $defaultTimeout;

    /**
     * PruneInterval specifies how often, in seconds, the cache of remote
     * session information is purged of unused data.
     *
     * @var How often session info is purged in seconds.
     */
    private $pruneInterval;

    /**
     * CommandWorkers is the number of incoming command REQUESTS that are
     * accepted at any given time. A new routine is started to service each
     * command request.
     *
     * @var int Maximum accepted command requests.
     */
    private $commandWorkers;

    /**
     * SessionWorkers is the number of command RESPONSES or notifications that
     * are buffered in memory at any given time.
     *
     * @var int Maximum allowed command responses.
     */
    private $sessionWorkers;
}
