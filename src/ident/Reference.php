<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use Rinq\Revision;

/**
 * Reference refers to a session at a specific revision.
 */
class Reference
{
    /**
     * Create a new Reference.
     *
     * @param SessionId $sessionId The unique ID session.
     * @param Revision  $revision  A "version" of a session.
     */
    public static function create(
        SessionId $sessionId,
        Revision $revision
    ): self {
        return new self($sessionId, $revision);
    }

    /**
     * @param SessionId $sessionId The unique ID session.
     * @param Revision  $revision  A "version" of a session.
     */
    private function __construct(SessionId $sessionId, Revision $revision)
    {
        $this->sessionId = $sessionId;
        $this->revision = $revision;
    }

    /**
     * @var SessionId The unique ID of the session.
     */
    public $sessionId;

    /**
     * Revision holds the "version" of a session. A session's revision is
     * incremented when a change is made to its attribute table. A session that
     * has never been modified, and hence has no attributes always has a
     * revision of 0.
     *
     * @var Revision A "version" of a session.
     */
    public $revision;
}
