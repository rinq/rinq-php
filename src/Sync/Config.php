<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync;

use Psr\Log\LoggerInterface;

/**
 * A set of config values used by the peers.
 */
final class Config
{
    /**
     * Create new configuration.
     *
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param float           $pruneInterval  How often session info is purged in seconds.
     */
    public static function create(
        LoggerInterface $logger,
        float $defaultTimeout = 5.0,
        float $pruneInterval = 180.0
    ): self {
        return new self(
            $logger,
            $defaultTimeout,
            $pruneInterval
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
     * @param LoggerInterface $logger         Defines a target for all the logs.
     * @param float           $defaultTimeout The default timeout in seconds.
     * @param float           $pruneInterval  How often session info is purged in seconds.
     */
    private function __construct(
        LoggerInterface $logger,
        float $defaultTimeout,
        float $pruneInterval
    ) {
        $this->logger = $logger;
        $this->defaultTimeout = $defaultTimeout;
        $this->pruneInterval = $pruneInterval;
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
}
