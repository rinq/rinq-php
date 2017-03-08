<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

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
     * Create a new Session ID.
     *
     * @param PeerId $peerId   The ID of the peer that owns the session.
     * @param int    $sequence The sequence ID of the session.
     */
    public static function create(PeerId $peerId, int $sequence): self
    {
        return new self($peerId, $sequence);
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
}
