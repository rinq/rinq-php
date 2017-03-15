<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\Reference;
use RuntimeException;

/**
 * Indicates a failure to update a revision because one or more of the updated attribute
 * are frozen.
 */
class FrozenAttributeException extends RuntimeException
{
    /**
     * @param Reference $reference The reference to the change-set that could not be modified.
     */
    public function __construct(Reference $reference)
    {
        parent::__construct(
            sprintf(
                'Can not update %s, the change-set references one or more frozen keys.',
                $reference
            )
        );

        $this->reference = $reference;
    }

    /**
     * @return Reference The reference to the change-set that could not be modified.
     */
    public function reference(): Reference
    {
        return $this->reference;
    }

    /**
     * @var Reference The reference to the change-set that could not be modified.
     */
    private $reference;
}
