<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Payload is the contents of a request or response.
 */
class Payload
{
    /**
     * Create a new payload with the given data.
     *
     * @param mixed $data Data which makes up the payload.
     */
    public static function create(mixed $data)
    {
        return new self($data);
    }

    /**
     * @param mixed $data Data which makes up the payload.
     */
    private function __construct(mixed $data)
    {
        $this->data = $data;
    }

    /**
     * @var mixed Data which makes up the payload.
     */
    private $data;
}
