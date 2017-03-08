<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

class MessageId
{
    private function __construct(Reference $reference, int $sequence)
    {
        $this->reference = $reference;
        $this->sequence = $sequence;
    }
}
