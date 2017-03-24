<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Internal\Command;

use Rinq\Internal\Service\Service;
use Rinq\Payload;
use Rinq\Context;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;

/**
 * Invoker is a low-level RPC interface, it is used to implement the "command
 * subsystem", as well as internal peer-to-peer requests.
 *
 * The terminology "call" refers to an invocation that expects a response,
 * whereas "execute" is an invocation where no response is required.
 */
interface Invoker
{
    /**
     * Sends a unicast command request to a specific peer and blocks until a
     * response is received or the context deadline is met.
     *
     * @throws ?
     */
    public function callUnicast(
        Context $context,
        MessageId $messageId,
        PeerId $target,
        string $namespace,
        string $command,
        Payload $payload,
        string &$traceId
    ): Payload;

    /**
     * Sends a load-balanced command request to the first available peer and
     * blocks until a response is received or the context deadline is met.
     *
     * @throws ?
     */
    public function callBalanced(
        Context $context,
        MessageId $messageId,
        string $namespace,
        string $command,
        Payload $payload,
        string &$traceId
    ): Payload;

    /**
     * Sends a load-balanced command request to the first available peer,
     * instructs it to send a response, but does not block.
     *
     * @throws ?
     */
    public function callBalancedAsync(
        Context $context,
        MessageId $messageId,
        string $namespace,
        string $command,
        Payload $payload,
        string &$traceId
    ): void;

    /**
     * Sets the asynchronous handler to use for a specific session.
     */
    public function setAsyncHandler(
        SessionId $sessionId,
        AsyncHandler $handler
    ): void;

    /**
     * Sends a load-balanced command request to the first available peer and
     * returns immediately.
     *
     * @throws ?
     */
    public function executeBalanced(
        Context $context,
        MessageId $messageId,
        string $namespace,
        string $command,
        Payload $payload,
        string &$traceId
    ): void;

    /**
     * Sends a multicast command request to the all available* peers and returns
     * immediately.
     *
     * @throws ?
     */
    public function executeMulticast(
        Context $context,
        MessageId $messageId,
        string $namespace,
        string $command,
        Payload $payload,
        string &$traceId
    ): void;
}
