<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Internal\Command;

use Rinq\CommandHandler;

/**
 * Server processes command requests made by an invoker.
 */
interface Server
{
    /**
     * Listen begins listening for command requests made by an invoker.
     *
     * @return bool True If server starterd listening in $namespace.
     *
     * @throws ?
     */
    public function listen(
        string $namespace,
        CommandHandler $handler
    ): bool;

    /**
     * Unlisten stops listening for command requests made by an invoker.
     *
     * @return bool True If server stopped listening in $namespace.
     *
     * @throws ?
     */
    public function unlisten($namespace): bool;
}
