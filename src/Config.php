<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Psr\Log\LoggerInterface;

final class Config
{
    /**
     * Create new configuration.
     *
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param int             $commandWorkers Maximum accepted command requests.
     * @param int             $sessionWorkers Maximum allowed command responses.
     * @param int             $pruneInterval  How often session info is purged.
     */
    public static function create(
        float $defaultTimeout,
        LoggerInterface $logger,
        int $commandWorkers,
        int $sessionWorkers,
        int $pruneInterval
    ): self {
        return new self(
            $defaultTimeout,
            $logger,
            $commandWorkers,
            $sessionWorkers,
            $pruneInterval
        );
    }

    /**
     * @return float The default timeout in seconds.
     */
    public function defaultTimeout(): float
    {
        return $this->defaultTimeout;
    }

    /**
     * @return LoggerInterface Defines a target for all the logs.
     */
    public function logger(): LoggerInterface
    {
        return $this->logger;
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
     * @return int How often session info is purged.
     */
    public function pruneInterval(): int
    {
        return $this->pruneInterval;
    }


    /**
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param int             $commandWorkers Maximum accepted command requests.
     * @param int             $sessionWorkers Maximum allowed command responses.
     * @param int             $pruneInterval  How often session info is purged.
     */
    private function __construct(
        float $defaultTimeout,
        LoggerInterface $logger,
        int $commandWorkers,
        int $sessionWorkers,
        int $pruneInterval
    ) {
        $this->defaultTimeout = $defaultTimeout;
        $this->logger = $logger;
        $this->commandWorkers = $commandWorkers;
        $this->sessionWorkers = $sessionWorkers;
        $this->pruneInterval = $pruneInterval;
    }

    /**
     * DefaultTimeout specifies the maximum amount of time to wait for a call to
	 * return. It is used if the context passed to Session::call() does not
	 * already have a deadline.
     *
     * @var float The default timeout in seconds.
     */
    private $defaultTimeout;

    /**
     * @var LoggerInterface Defines a target for all the logs.
     */
    private $logger;

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

    /**
     * PruneInterval specifies how often the cache of remote session information
	 * is purged of unused data.
     * @var How often session info is purged.
     */
    private $pruneInterval;
}
