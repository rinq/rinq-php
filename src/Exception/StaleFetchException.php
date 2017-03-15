<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\Reference;
use RuntimeException;

/**
 * Indicates an error fetching one or more attributes as they were stale.
 */
class StaleFetchException extends RuntimeException implements StaleException
{
    /**
     * @param Reference $reference The reference that could not be retrieved.
     */
    public function __construct(Reference $reference)
    {
        parent::__construct(
            sprintf(
                'Can not fetch attributes at %s, one or more attributes have ' .
                    'been modified since that revision.',
                $reference
            )
        );

        $this->reference = $reference;
    }

    /**
     * @return Reference The reference that could not be retrieved.
     */
    public function reference(): Reference
    {
        return $this->reference;
    }

    /**
     * @var Reference The reference that could not be retrieved.
     */
    private $reference;
}
