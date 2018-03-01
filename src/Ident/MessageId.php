<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

/**
 * MessageID uniquely identifies a message that originated from a session.
 */
class MessageId
{
    /**
     * @var Referenece Refers to a session at a specific revision.
     */
    public $reference;

    /**
     * @var int The sequence ID of the message.
     */
    public $sequence;

    /**
     * Create a new message ID.
     *
     * @param Reference $reference Refers to a session at a specific revision.
     * @param int       $sequence  The sequence ID of the message.
     */
    public static function create(Reference $reference, int $sequence): self
    {
        return new self($reference, $sequence);
    }

    public static function createFromString(string $messageId)
    {
        $parts = explode('#', $messageId);

        if (count($parts) !== 2 || !ctype_digit($parts[1])) {
            throw new RuntimeException(
                sprintf('Message ID %s is invalid.', $messageId)
            );
        }

        return self::create(
            Reference::createFromString($parts[0]),
            (int) $parts[1]
        );
    }

    public function shortString(): string
    {
        return sprintf(
            '%s#%d',
            $this->reference->shortString(),
            $this->sequence
        );
    }

    public function __toString()
    {
        return sprintf('%s#%d', $this->reference, $this->sequence);
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
}
