<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Exception\ClientException;
use Bunny\Channel;
use Bunny\Client;
use Bunny\Message as BunnyMessage;
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
        Client $broker,
        InvokerLogging $logger,
        float $defaultTimeout // last becuase the async version is followed by preFetch
)
    {
        $this->peerId = $peerId;
        $this->queues = $queues;
        $this->broker = $broker;
        $this->logger = $logger;
        $this->defaultTimeout = $defaultTimeout;

        $this->publishChannel = $broker->channel();
        $this->consumeChannel = $broker->channel();
        $this->handlers = [];
        $this->callResponse = null; // the response of a call
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
        string &$traceId = null
    ) {
        // msg := &amqp.Publishing{
        // MessageId: msgID.String(),
        // Priority:  callBalancedPriority,
        // }

        $traceId = $context->traceId();
        if (null === $traceId || $traceId === $messageId) {
            $traceId = $messageId;
        }

        // traceID := amqputil.PackTrace(ctx, msg)
        $headers['correlation-id'] = strval($traceId);
        $headers['message-id'] = strval($messageId);

        Message::packRequest(
            $headers,
            $namespace,
            $command,
            Message::replyCorrelated
        );

        $this->logger->logBalancedCallBegin(
            $this->peerId,
            $messageId,
            $namespace,
            $command,
            $traceId,
            $payload
        );
        $result = $this->call(
            $context,
            Exchanges::balancedExchange,
            $namespace,
            $payload,
            $headers
        );
        $this->logger->logCallEnd(
            $this->peerId,
            $messageId,
            $namespace,
            $command,
            $traceId,
            $result
            //err   // TODO: need to captures error
        );

        return $result;
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

        $this->logger->logBalancedExecute(
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

        $this->logger->logMulticaseExecute(
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
        $this->consumeChannel->qos(
            0,  // $prefetchSize = 0,
            1   // $prefetchCount = 0
        );

        // TODO:
        // i.channel.NotifyClose(i.amqpClosed)

        $queue = $this->queues->responseQueue($this->peerId);

        $this->consumeChannel->queueDeclare(
            $queue,
            false,  // passive
            false,  // durable
            true    // exclusive
        );

        $this->consumeChannel->queueBind(
            $queue,
            Exchanges::responseExchange,
            $this->peerId . '.*'
        );

        $this->consumeChannel->consume(
            function(BunnyMessage $message, Channel $channel, Client $bunny) {
                $this->isWaiting = false;
                $this->callResponse = Message::unpackResponse($message);
                $bunny->stop();  // stop consuming as we have our response.
            },
            $queue,
            $queue, // use queue name as consumer tag
            false,  // no local
            false,  // autoAck
            true    // exclusive
        );

        // synchronous invoker has a preFetch of 1
        $this->logger->logInvokerStart($this->peerId, 1);
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

    public function stop()
    {
        $this->logger->logInvokerStopping($this->peerId, 0);

        $this->publishChannel = null; // no more publishing for us.
        $this->isWaiting = false;

        // TODO: do we need this???
        // foreach ($this->handlers as $namespace => $handler) {
        //     $this->unlisten($namespace);
        // }

        $this->logger->logInvokerStop($this->peerId);
    }

    /**
     * publish sends an command request to the broker.
     */
    private function publish(
        string $exchange,
        string $key,
        $payload,
        array $headers = []
    ) {
        if ($exchange === Exchanges::balancedExchange) {
            $queue = $this->queues->get($this->publishChannel, $key);
        }

        if (null === $this->publishChannel) {
            throw new ChannelClosedException();
        }

        $this->publishChannel->publish(
            CBOREncoder::encode($payload),  // message
            $headers,                       // headers
            $exchange,                      // exchange
            $key                            // routing key
        );
    }

    private function call(
        Context $context,
        string $exchange,
        string $key,
        $payload,
        array $headers
    ) {
        $this->isWaiting = true;

        $this->publish($exchange, $key, $payload, $headers);

        do {
            // TODO: move to helper function
            try {
                // TODO: time run() and subtract from $timeout
                $this->broker->run($context->timeout()?: $this->defaultTimeout);
            } catch (ClientException $e) {
                $error = error_get_last();
                if (stripos($error['message'], 'Interrupted system call') === false) {
                    throw $e;
                }

                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
            }
            // TODO: handle timeout value a bit better
        } while ($this->isWaiting);

        // if (!$response) {
        //     // timed out
        // }

        if ($this->publishChannel === null) {
            $this->consumeChannel->close();
        }

        return $this->callResponse;
    }

    private $peerId;
    private $queues;
    private $broker;
    private $logger;
    private $defaultTimeout;

    private $publishChannel;
    private $consumeChannel;
    private $handlers;
    private $isWaiting;
    private $callResponse;
}
