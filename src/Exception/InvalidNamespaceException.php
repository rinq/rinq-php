<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;

/**
 * Indicates the namespace is invalid. A namespace MUST NOT be empty, contain
 * invalid charaters or match a reserved keyword.
 */
class InvalidNamespaceException extends RuntimeException
{
    public static function emptyNamespace(): self
    {
        return new self('Namespace must not be empty.');
    }

    public static function reservedNamespace(string $namespace): self
    {
        return new self(sprintf('Namespace \'%s\' is reserved.', $namespace));
    }

    public static function invalidCharacters(string $namespace): self
    {
        return new self(
            sprintf(
                'Namespace \'%s\' contains invalid characters.',
                $namespace
            )
        );
    }

    public function __construct($reason)
    {
        parent::__construct($reason);
    }
}
