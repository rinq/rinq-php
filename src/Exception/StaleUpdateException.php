<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\ident\Reference;
use RuntimeException;

class StaleUpdateException extends RuntimeException implements ShouldRetryException
{
    /**
     * @param Reference $reference The reference that could not be updated.
     */
    public function __construct(Reference $reference)
    {
        parent::__construct(
            sprintf(
                'Can not update or close %s, the session has been modified ' .
                    'since that revision.',
                $reference
            )
        );

        $this->reference = $reference;
    }

    /**
     * @return Reference The reference that could not be updated.
     */
    public function reference(): Reference
    {
        return $this->reference;
    }

    /**
     * @var Reference The reference that could not be updated.
     */
    private $reference;
}
