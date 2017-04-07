<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use CBOR\CBOREncoder;
use Rinq\Context;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;
use Rinq\Ident\SessionId;
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
    public function __construct(
        PeerId $peerId,
        Queues $queues,
        Channel $channel,
        InvokerLogging $invokerLogger,
        float $defaultTimeout // last becuase the async version is followed by preFetch
)
    {
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
        $payload,
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
        $payload,
        string &$traceId
    ) {
        // call bunny run for $timeout and wait for response
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
        $payload,
        string &$traceId
    ): void {
    }

    /**
     * Sets the asynchronous handler to use for a specific session.
     */
    public function setAsyncHandler(
        SessionId $sessionId,
        callable $handler = null
    ): void {
        // TODO: should this only be in the async invoker?
        // if (null === $handler) {
        //     unset($this->handlers[$sessionId]);
        //
        //     return;
        // }
        //
        // $this->handlers[$sessionId] = $handler;
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
        $payload,
        string &$traceId
    ): void {
        $this->send(
            $context,
            Exchanges::balancedExchange,
            $namespace,
            $payload,
            [
                'message-id' => $messageId,
                'x-max-priority' => 3,  // TODO: need proper priorities
                Message::namespaceHeader => $namespace,
                Message::commandHeader => $command,
                'reply-to' => Message::replyNone,
                // TODO: Missing delivery mode
                // DeliveryMode: amqp.Persistent,
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
        $payload,
        string &$traceId
    ): void {
        $this->send(
            $context,
            Exchanges::multicastExchange,
            $namespace,
            $payload,
            [
                'message-id' => $messageId,
                'x-max-priority' => 3,  // TODO: need proper priorities
                Message::namespaceHeader => $namespace,
                Message::commandHeader => $command,
                'reply-to' => Message::replyNone,
            ]
        );

        $this->invokerLogger->logMulticaseExecute(
            $this->peerId,
            $messageId,
            $namespace,
            $command,
            $context->traceId(),
            $payload
        );
    }

    public function initialize()
    {
        $this->channel->qos(
            0,  // $prefetchSize = 0,
            1   // $prefetchCount = 0
        );

        // TODO:
        // i.channel.NotifyClose(i.amqpClosed)

        $queue = $this->queues->responseQueue($this->peerId);

        $this->channel->queueDeclare(
            $queue,
            false,  // passive
            false,  // durable
            true    // exclusive
        );

        $this->channel->queueBind(
            $queue,
            Exchanges::responseExchange,
            $this->peerId . '.*'
        );

        $this->channel->consume(
            function(Message $message, Channel $channel, Bunny $bunny) {
                // $this->consumemethod, // TODO handle response
            },
            $queue,
            $queue, // use queue name as consumer tag
            false,  // no local
            false,  // autoAck
            true    // exclusive
        );

        // synchronous invoker has a preFetch of 1
        $this->invokerLogger->logInvokerStart($this->peerId, 1);
    }

    /**
     * send publishes a message for a command request.
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
     * publish sends an command request to the broker.
     */
    public function publish(
        string $exchange,
        string $key,
        $payload,
        array $headers = []
    ) {
        if ($exchange === Exchanges::balancedExchange) {
            $queue = $this->queues->get($channel, $key);
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
