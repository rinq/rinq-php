<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * A context is a request that carries a timeout and traceId.
 *
 * Timeout is also used to derive a deadline, that is, a point time time after
 * which the context is no longer valid.
 */
final class Context
{
    /**
     * Create a new context.
     *
     * @param int    $timeout The timeout in seconds.
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
     * @return string The unique identifier for the context.
     */
    public function traceId(): string
    {
        return $this->traceId;
    }

    /**
     * @param int    $timeout The timeout in seconds.
     * @param string $traceId The unique identifier for the context.
     */
    private function __construct(int $timeout, string $traceId)
    {
        $this->timeout = $timeout;
        $this->traceId = $traceId;
    }

    /**
     * Timeout is the number of second the context is valid for.
     *
     * @var int Timeout in seconds.
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
