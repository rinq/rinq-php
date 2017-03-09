<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Attr is a session attribute.
 *
 * Sessions contain a versioned key/value store. See the Session interface for
 * more information.
 */
class Attribute
{
    public static function create(string $key, mixed $value)
    {
        return new self($key, $value);
    }

    private function __construct(string $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isFrozen = false;
    }

    /**
     * Key is an application-defined identifier for the attribute. Keys are
     * unique within a session. Any valid UTF-8 string can be used a key,
     * including the empty string.
     *
     * @var string The key is an application-defined identifier.
     */
    private $key;

    /**
     * Value is the attribute's value. Any valid UTF-8 string can be used as a
     * value, including the empty string.
     *
     * @var mixed The attributes value.
     */
    private $value;

    /**
     * True if the attribute is "frozen" such that it can never be altered again
     * (for a given session).
     *
     * @var bool true if the attribute is "frozen".
     */
    private $isFrozen;
}