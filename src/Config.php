<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Psr\Log\LoggerInterface;

final class Config
{
    /**
     * Create new configuration.
     *
     * @param int             $defaultTimeout  The default timeout.
     * @param LoggerInterface $logger          Defines a target for all the logs.
     * @param int             $commandPrefetch Maximum accepted command requests.
     * @param int             $sessionPreFetch Maximum allowed command responses.
     * @param int             $pruneInterval   How often session info is purged.
     */
    public static function create(
        int $defaultTimeout,
        LoggerInterface $logger,
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

    /**
     * @return int $defaultTimeout The default timeout.
     */
    public function defaultTimeout(): int
    {
        return $this->defaultTimeout;
    }

    /**
     * @return LoggerInterface $logger Defines a target for all the logs.
     */
    public function logger (): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return int $commandPrefetch Maximum accepted command requests.
     */
    public function commandPreFetch(): int
    {
        return $this->commandPreFetch;
    }

    /**
     * @return int $sessionPreFetch Maximum allowed command responses.
     */
    public function sessionPreFetch(): int
    {
        return $this->sessionPreFetch;
    }

    /**
     * @return int $pruneInterval How often session info is purged.
     */
    public function pruneInterval(): int
    {
        return $this->pruneInterval;
    }


    /**
     * @param int             $defaultTimeout  The default timeout.
     * @param LoggerInterface $logger          Defines a target for all the logs.
     * @param int             $commandPrefetch Maximum accepted command requests.
     * @param int             $sessionPreFetch Maximum allowed command responses.
     * @param int             $pruneInterval   How often session info is purged.
     */
    private function __construct(
        int $defaultTimeout,
        LoggerInterface $logger,
        int $commandPreFetch,
        int $sessionPreFetch,
        int $pruneInterval
    ) {
        $this->defaultTimeout = $defaultTimeout;
        $this->logger = $logger;
        $this->commandPreFetch = $commandPreFetch;
        $this->sessionPreFetch = $sessionPreFetch;
        $this->pruneInterval = $pruneInterval;
    }

    /**
     * DefaultTimeout specifies the maximum amount of time to wait for a call to
	 * return. It is used if the context passed to Session.call() does not
	 * already have a deadline.
     *
     * @var int The default timeout.
     */
    private $defaultTimeout;

    /**
     * @var LoggerInterface Defines a target for all the logs.
     */
    private $logger;

    /**
     * CommandPreFetch is the number of incoming command REQUESTS that are
	 * accepted at any given time. A new routine is started to service each
	 * command request.
     *
     * @var int Maximum accepted command requests.
     */
    private $commandPreFetch;

    /**
     * SessionPreFetch is the number of command RESPONSES or notifications that
     * are buffered in memory at any given time.
     *
     * @var int Maximum allowed command responses.
     */
    private $sessionPreFetch;

    /**
     * PruneInterval specifies how often the cache of remote session information
	 * is purged of unused data.
     * @var How often session info is purged.
     */
    private $pruneInterval;
}
