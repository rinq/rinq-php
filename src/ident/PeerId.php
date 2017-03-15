<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

/**
 * PeerID uniquely identifies a peer within a network.
 *
 * Peer IDs contain a clock component and a random 16-bit integer component.
 * They are rendered in hexadecimal encoding, with a hypen separating the two
 * components, such as "58AEE146-191C".
 *
 * For a given network, the random component is guaranteed to be unique at any
 * given time; and, assuming a stable system clock it is highly likely that the
 * ID is unique across time. This makes peer IDs useful for tracking peer
 * behaviour in logs.
 *
 * All other IDs generated by a peer, such as {@see SessionId} and
 * {@see MessageId} are derived from the peer ID.
 */
class PeerId
{
    /**
     * Clock is a time-based portion of the ID, this helps uniquely identify
     * peer IDs over longer time-scales, such as when looking back through
     * logs, etc.
     *
     * @var int The time-based portion of the ID.
     */
    public $clock;

    /**
     * Rand is a unique number identifying this peer within a network at any
     * given time. It is generated randomly and then reserved when the peer
     * connects to the network.
     *
     * @var int Random, unique identifier for this peer.
     */
    public $rand;

    /**
     * Create a new Peer ID.
     *
     * @param int $clock The time-based portion of the ID.
     * @param int $rand  Random, unique identifier for this peer.
     */
    public static function create(int $clock, int $rand): self
    {
        return new self($clock, $rand);
    }

    public function __toString()
    {
        return sprintf('%X-%04X', dechex($this->clock), dechex($this->rand));
    }

    /**
     * @param int $clock The time-based portion of the ID.
     * @param int $rand  Random, unique identifier for this peer.
     */
    private function __construct(int $clock, int $rand)
    {
        $this->clock = $clock;
        $this->rand = $rand;
    }
}
