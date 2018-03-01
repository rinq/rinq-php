<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

/**
 * SessionID uniquely identifies a session within a network.
 *
 * Session IDs contain a peer component, and a sequence component.
 * They are rendereds as a peer ID, followed by a period, then the sequence
 * component as a decimal, such as "58AEE146-191C.45".
 *
 * Because the peer ID is embedded, the same uniqueness guarantees apply to the
 * session ID as to the peer ID.
 */
class SessionId
{
    /**
     * @var PeerId The ID of the peer that owns the session.
     */
    public $peerId;

    /**
     * Seq is a monotonically increasing sequence allocated to each session in
     * the order it is created by the owning peer. Application sessions begin
     * with a sequence value of 1. The sequnce value zero is reserved for the
     * "zero-session", which is used for internal operations.
     *
     * @var int The sequence ID of the session.
     */
    public $sequence;

    /**
     * Create a new Session ID.
     *
     * @param PeerId $peerId   The ID of the peer that owns the session.
     * @param int    $sequence The sequence ID of the session.
     */
    public static function create(PeerId $peerId, int $sequence): self
    {
        return new self($peerId, $sequence);
    }

    public static function createFromString(string $sessionId)
    {
        $parts = explode('.', $sessionId);

        if (count($parts) !== 2 || !ctype_digit($parts[1])) {
            throw new RuntimeException(
                sprintf('Session ID %s is invalid.', $sessionId)
            );
        }

        return self::create(
            PeerId::createFromString($parts[0]),
            (int) $parts[1]
        );
    }

    public function shortString()
    {
        return sprintf('%s.%d', $this->peerId->shortString(), $this->sequence);
    }

    public function __toString()
    {
        return sprintf('%s.%d', $this->peerId, $this->sequence);
    }

    /**
     * @param PeerId $peerId   The ID of the peer that owns the session.
     * @param int    $sequence The sequence ID of the session.
     */
    private function __construct(PeerId $peerId, int $sequence)
    {
        $this->peerId = $peerId;
        $this->sequence = $sequence;
    }
}
