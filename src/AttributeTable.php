<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * AttributeTable is a map of of attribute key to Attribute.
 */
final class AttributeTable
{
    /**
     * Create a new attribute table with the given map of attributes.
     *
     * @param array $attributes Map of attributes.
     */
    public static function create(array $attributes): self
    {
        return new self($attributes);
    }

    /**
     * @return array Map of attributes.
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes Map of attributes.
     */
    private function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @var array<string, Attribute> Map of attributes.
     */
    private $attributes;
}
