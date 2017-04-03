<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use CBOR\CBOREncoder;
use Rinq\Context;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;
use Rinq\Internal\Command\Invoker as InvokerInterface;

/**
 * Invoker is a low-level RPC interface, it is used to implement the "command
 * subsystem", as well as internal peer-to-peer requests.
 *
 * The terminology "call" refers to an invocation that expects a response,
 * whereas "execute" is an invocation where no response is required.
 */
class Invoker implements InvokerInterface
{
    private function __construct(
        PeerId $peerId,
        Queues $queues,
        Channel $channel,
        InvokerLogging $invokerLogger,
        Message $message,
        int $defaultTimeout, // last becuase the async version is followed by preFetch
    ) {
        $this->peerId = $peerId;
        $this->queues = $queues;
        $this->channel = $channel;
        $this->invokerLogger = $invokerLogger;
        $this->defaultTimeout = $defaultTimeout;
    }
    /**
     * Sends a unicast command request to a specific peer and blocks until a
     * response is received or the context deadline is met.
     *
     * @return mixed The response of the invoked call.
     *
     * @throws ?
     */
    public function callUnicast(
        Context $context,
        MessageId $messageId,
        PeerId $target,
        string $namespace,
        string $command,
        mixed $payload,
        string &$traceId
    ) {
    }

    /**
     * Sends a load-balanced command request to the first available peer and
     * blocks until a response is received or the context deadline is met.
     *
     * @return mixed The response of the invoked call.
     *
     * @throws ?
     */
    public function callBalanced(
        Context $context,
        MessageId $messageId,
        string $namespace,
        string $command,
        mixed $payload,
        string &$traceId
    ) {
    }

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
        mixed $payload,
        string &$traceId
    ): void {
    }

    /**
     * Sets the asynchronous handler to use for a specific session.
     */
    public function setAsyncHandler(
        SessionId $sessionId,
        AsyncHandler $handler = null
    ): void {
        if (null === $handler) {
            unset($this->handlers[$sessionId]);
            return;
        }

        $this->handlers[$sessionId] = $handler;
    }

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
        mixed $payload,
        string &$traceId
    ): void {
        $this->send(
            $context,
            Exchanges::balancedExchange,
            $namespace,
            $payload,
            [
                'correlation-id' => $messageId,
                'x-max-priority' => 10, // TODO: need proper priorities
                Message::namespaceHeader => $namespace,
                Message::commandHeader => $command,
                'reply-to' => Message::replyNone,
            ]
        );

        $this->invokerLogger->logBalancedExecute(
            $this->peerId,
            $messageId,
            $namespace,
            $command,
            $context->traceId(),
            $payload
        );
    }

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
        mixed $payload,
        string &$traceId
    ): void {
    }

    /**
     * send publishes a message for a command request
     */
    public function send(
        Context $context,
        string $exchange,
        string $key,
        string $message,
        array $headers = []
    ) {
        // TODO: Context is not used??
        $this->publish($exchange, $key, $message, $headers);
    }

    /**
     * publish sends an command request to the broker
     */
    public function publish(
        string $exchange,
        string $key,
        mixed $payload,
        array $headers = []
    ) {
        if ($exchange === Exchanges::balancedExchange) {
            $queue = $this->queues.get($channel, $key);
        }

        $this->channel->publish(
            CBOREncoder::encode($payload),  // message
            $headers,                       // headers
            $exchange,                      // exchange
            $key                            // routing key
        );
    }


    private $peerId;
    private $queues;
    private $channel;
    private $invokerLogger;
    private $defaultTimeout;
    private $handlers = [];
}
