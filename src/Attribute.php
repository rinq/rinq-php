<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Attribute is a session attribute.
 *
 * Sessions contain a versioned key/value store. See the Session interface for
 * more information.
 */
final class Attribute
{
    /**
     * Create a new attribute with the given key/value.
     *
     * @param string $key   The key is an application-defined identifier.
     * @param mixed  $value The attributes value.
     */
    public static function create(string $key, mixed $value): self
    {
        return new self($key, $value, false);
    }

    /**
     * Convenience method to create an attribute with the given key/value and
     * marked as frozen.
     *
     * @param string $key   The key is an application-defined identifier.
     * @param mixed  $value The attributes value.
     */
    public static function freeze(string $key, mixed $value): self
    {
        return new self($key, $value, true);
    }

    /**
     * @return string The key is an application-defined identifier.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return mixed The attributes value.
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return bool True if the attribute is "frozen".
     */
    public function isFrozen(): bool
    {
        return $this->isFrozen;
    }

    /**
     * Create a new attribute with the given key/value.
     *
     * @param string $key      The key is an application-defined identifier.
     * @param mixed  $value    The attributes value.
     * @param bool   $isFrozen True if the attribute is "frozen".
     */
    private function __construct(string $key, mixed $value, bool $isFrozen)
    {
        $this->key = $key;
        $this->value = $value;
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
     * @var bool True if the attribute is "frozen".
     */
    private $isFrozen;
}
