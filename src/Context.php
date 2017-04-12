<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Context carries timeout and other request-scoped values across API boundaries
 * and between processes.
 *
 * Timeout specifies the maximum amount of time (in seconds) that an operation
 * may take before it is cancelled.
 */
final class Context
{
    /**
     * Create a new context.
     *
     * @param float       $timeout The timeout in seconds.
     * @param string|null $traceId The unique identifier for the context.
     */
    public static function create(float $timeout, string $traceId = null): self
    {
        return new self($timeout, $traceId);
    }

    /**
     * @return float The timeout in seconds.
     */
    public function timeout(): float
    {
        return $this->timeout;
    }

    /**
     * @return string The unique identifier for the context.
     */
    public function traceId(): ? string
    {
        return $this->traceId;
    }

    /**
     * @param float       $timeout The timeout in seconds.
     * @param string|null $traceId The unique identifier for the context.
     */
    private function __construct(float $timeout, string $traceId = null)
    {
        $this->timeout = $timeout;
        $this->traceId = $traceId;
    }

    /**
     * Timeout is the number of second the context is valid for.
     *
     * @var float Timeout in seconds.
     */
    private $timeout;

    /**
     * Trace id is used to track a single command throughout its invocation and
     * can possibly branch off to multiple calls.
     *
     * @var string The unique identifier for the context.
     */
    private $traceId;
}
