<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

/**
 * MessageID uniquely identifies a message that originated from a session.
 */
class MessageId
{
    /**
     * Create a new message id.
     *
     * @param Reference $reference Refers to a session at a specific revision.
     * @param int       $sequence  The sequence ID of the message.
     */
    public static function create(Reference $reference, int $sequence): self
    {
        return new self($reference, $sequence);
    }

    /**
     * @param Reference $reference Refers to a session at a specific revision.
     * @param int       $sequence  The sequence ID of the message.
     */
    private function __construct(Reference $reference, int $sequence)
    {
        $this->reference = $reference;
        $this->sequence = $sequence;
    }

    /**
     * @var Referenece Refers to a session at a specific revision.
     */
    public $reference;

    /**
     * @var int The sequence ID of the message.
     */
    public $sequence;
}
