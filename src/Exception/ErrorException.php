<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;

/**
 * CommandError is an error (as opposed to a Failure) sent in response to a
 * command.
 */
final class ErrorException extends RuntimeException
{
    /**
     * @param string $type The type of error.
     */
    public static  function create(string $type, string $message = null)
    {
        return new self($type, $message);
    }

    /**
     * Type is an application-defined string identifying the error.
     * They serve the same purpose as an error code. They should be concise
     * and easily understandable within the context of the application's API.
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string      $type    The type of error.
     * @param string|null $message Optional human-readable description of the error.
     *
     * @throws RuntimeException If the error type is empty.
     */
    public function __construct(string $type, string $message = null)
    {
        if ($type === '') {
            throw new RuntimeException('Failure type is empty.');
        }

        if (null === $message) {
            $message = 'Unexpected error exception.';
        }

        parent::__construct($message);

        $this->type = $type;
    }

    private $type;
}
