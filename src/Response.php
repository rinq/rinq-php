<?php

declare(strict_types=1);  // @codeCoverageIgnore

namespace Rinq;

/**
 * Response sends a reply to incoming command requests.
 */
interface Response
{
    /**
     * If the response is not required, any payload data sent is discarded.
     * The response must always be closed, even if IsRequired() returns false.
     *
     * @return bool True if the sender is waiting for the response.
     */
    public function isRequired(): bool;

    /**
     * IsClosed true If the response has already been closed.
     */
    public function isClosed(): bool;

    /**
     * Send a payload to the source session and closes the response.
     *
     * @param mixed $payload The body of the response.
     *
     * @throws BlahException If the response has already been closed.
     */
    public function done($payload): void;

    /**
     * Send an error to the source session and closes the response.
     *
     * @throws BlahException If the response has already been closed.
     */
    public function error($error): void;

    /**
     * A convenience method that creates a Failure and passes it to
     * {@see $this->error()} method. The created failure is returned.
     *
     * The failure $type is used verbatim. The failure message is formatted
     * according to the format specifier $message, interpolated with values from
     * $vars.
     *
     * @throws BlahException If the response has already been closed or if failureType is empty.
     */
    public function fail(string $type, string $message, $vars): Failure;

    /**
     * Close finalizes the response.
     *
     * If the origin session is expecting response it will receive a null
     * payload.
     *
     * It is not an error to close a responder multiple times. The return value
     * is true the first time Close() is called, and false on subsequent calls.
     */
    public function close(): bool;
}
