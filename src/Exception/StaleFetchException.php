<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\ident\Reference;
use RuntimeException;

class StaleFetchException extends RuntimeException implements ShouldRetryException
{
    /**
     * @param Reference $reference The reference that could not be retrieved.
     */
    public function __construct(Reference $reference)
    {
        parent::__construct(
            sprintf(
                'can not fetch attributes at %s, one or more attributes have ' .
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
