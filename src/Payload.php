<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

class Payload
{
    private function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @var mixed The contents of the payload.
     */
    private $data;
}
