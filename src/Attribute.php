<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Attr is a sesssion attribute.
 *
 * Sessions contain a versioned key/value store. See the Session interface for
 * more information.
 */
class Attribute
{
    /**
     * Key is an application-defined identifier for the attribute. Keys are
     * unique within a session. Any valid UTF-8 string can be used a key,
     * including the empty string.
     */
    private $key;

    /**
     * Value is the attribute's value. Any valid UTF-8 string can be used as a
     * value, including the empty string.
     */
    private $value;

    /**
     * @var bool true if the attribute is "frozen" such that it can never be
     *           altered again (for a given session).
     */
    private $isFrozen;
}
