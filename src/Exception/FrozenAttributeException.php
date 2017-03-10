<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\Attribute;
use RuntimeException;

class FrozenAttributeException extends RuntimeException
{
    /**
     * @param Attribute $attribute The attribute that could not be modified.
     */
    public function __construct(Attribute $attribute)
    {
        parent::__construct(
            sprintf(
                'Attribute %s is frozen and can not be modified.',
                $attribute
            )
        );

        $this->attribute = $attribute;
    }

    /**
     * @return Attribute The attribute that could not be modified.
     */
    public function attribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @var Attribute The attribute that could not be modified.
     */
    private $attribute;
}
