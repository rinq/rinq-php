<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * A context is a request that carries a timeout and traceId.
 *
 * Timeout is the number of second the context is valid for.
 *
 * Timeout is also used to derive a deadline, that is, a point time time after
 * which the context is no longer valid.
 *
 * Trace id is used to track a single command throughout its invocation.
 */
class Context
{
    /**
     * Create a new context.
     *
     * @param int    $timeout How long in seconds the context is valid for.
     * @param string $traceId The unique identifier for the context.
     */
    public static function create(int $timeout, string $traceId): self
    {
        return new self($timeout, $traceId);
    }

    /**
     * @return int The timeout in seconds.
     */
    public function timeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return string The trace id.
     */
    public function traceId(): string
    {
        return $this->traceId;
    }

    /**
     * @param int    $timeout How long in seconds the context is valid for.
     * @param string $traceId The unique identifier for the context.
     */
    private function __construct(int $timeout, string $traceId)
    {
        $this->timeout = $timeout;
        $this->traceId = $traceId;
    }

    /**
     * @var int How long in seconds the context is valid for.
     */
    private $timeout;

    /**
     * @var string The unique identifier for the context.
     */
    private $traceId;
}
