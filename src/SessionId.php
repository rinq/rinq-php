<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * SessionID uniquely identifies a session within a network.
 *
 * Session IDs contain a peer component, and a 32-but sequence component.
 * They are rendereds as a peer ID, followed by a period, then the sequence
 * component as a decimal, such as "58AEE146-191C.45".
 *
 * Because the peer ID is embedded, the same uniqueness guarantees apply to the
 * session ID as to the peer ID.
 */
class SessionId
{
    /**
	 * Peer is the ID of the peer that owns the session.
     *
     * @var PeerId The peer id.
     */
	private $peer;

    /**
	 * Seq is a monotonically increasing sequence allocated to each session in
	 * the order it is created by the owning peer. Application sessions begin
	 * with a sequence value of 1. The sequnce value zero is reserved for the
	 * "zero-session", which is used for internal operations.
     *
     * @var int The sequence identifier.
     */
	private $seq;
}
