<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\SessionId;
use RuntimeException;

/**
 * Session does not exist or has since been closed and is no longer available.
 */
class NotFoundException extends RuntimeException
{
    /**
     * @param SessionId $sessionId The session id that could not be found.
     */
    public function __construct(SessionId $sessionId)
    {
        parent::__construct(sprintf('Session %s not found.', $sessionId));

        $this->id = $sessionId;
    }

    /**
     * @return SessionId The session id that could not be found.
     */
    public function id(): SessionId
    {
        return $this->id;
    }

    /**
     * @var SessionId The session id that could not be found.
     */
    private $id;
}
