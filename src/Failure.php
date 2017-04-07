<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;
use RuntimeException;

/**
 * Failure is an application-defined command error.
 *
 * Failures are used to indicate an error that is "expected" within the domain
 * of the command that produced it. The for part of the command's API and should
 * usually be handled by the caller.
 *
 * Failures can be produced by a command handler by calling Response.Fail() or
 * passing a Failure value to Response.Error().
 */
final class Failure
{
    /**
     * @param string      $type    The type of failure.
     * @param string|null $message Optional human-readable description of the failure.
     * @param mixed       $payload Optional application-defined payload.
     */
    public function create(string $type, string $message = null, $payload = null)
    {
        return self($type, $message, $payload);
    }

    /**
     * Type is an application-defined string identifying the failure.
     * They serve the same purpose as an error code. They should be concise
     * and easily understandable within the context of the application's API.
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Message is an optional human-readable description of the failure.
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * Payload is an optional application-defined payload.
     *
     * @return mixed
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * @param string      $type    The type of failure.
     * @param string|null $message Optional human-readable description of the failure.
     * @param mixed       $payload Optional application-defined payload.
     *
     * @throws RuntimeException If the failure type is empty.
     */
    public function __construct(string $type, string $message, $payload)
    {
        if ($type === '') {
            throw new RuntimeException('Failure type is empty.');
        }

        $this->type = $type;
        $this->message = $message;
        $this->payload = $payload;
    }

    private $type;
    private $message;
    private $payload;
}
