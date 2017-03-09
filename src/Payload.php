<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Payload represents the contents of a request or response.
 */
final class Payload
{
    /**
     * Create a new payload containing the given data.
     *
     * @param mixed $data The data which makes up the payload.
     */
    public static function create(mixed $data): self
    {
        return new self($data);
    }

    /**
     * @return mixed The data which makes up the payload.
     */
    public function data(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data The data which makes up the payload.
     */
    private function __construct(mixed $data)
    {
        $this->data = $data;
    }

    /**
     * @var mixed The data which makes up the payload.
     */
    private $data;
}
