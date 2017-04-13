<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

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
final class FailureException extends RuntimeException
{
    /**
     * @param string      $type    The type of failure.
     * @param string|null $message Optional human-readable description of the failure.
     * @param mixed       $payload Optional application-defined payload.
     *
     * @throws RuntimeException If the failure type is empty.
     */
    public function __construct(string $type, string $message = null, $payload = null)
    {
        if ($type === '') {
            throw new RuntimeException('Failure type cannot be empty.');
        }

        if (null === $message) {
            $message = 'Unknown failure.';
        }

        parent::__construct($message);

        $this->type = $type;
        $this->payload = $payload;
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
     * Payload is an optional application-defined payload.
     *
     * @return mixed
     */
    public function payload()
    {
        return $this->payload;
    }

    private $type;
    private $payload;
}
