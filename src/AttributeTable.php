<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * AttributeTable is a map of of attribute key to Attribute.
 */
class AttributeTable
{
    /**
     * Create a new attribute table with the given map of attributes.
     *
     * @param array $attributes Map of attributes.
     */
    public static function create(array $attributes)
    {
        return new self($attributes);
    }

    /**
     * @param array $attributes Map of attributes.
     */
    private function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @var array Map of attributes.
     */
    private $attributes;
}
