<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message as BunnyMessage;
use CBOR\CBOREncoder;
use Rinq\Failure;
use Rinq\Request;
use Rinq\Context;
use Rinq\Revision;
use Rinq\Ident\PeerId;
use Rinq\Ident\MessageId;
use Rinq\Internal\Command\Server as ServerInterface;

/**
 * Server processes command requests made by an invoker.
 */
class Server implements ServerInterface
{
    public function __construct(
        PeerId $peerId,
        Client $broker,
        ServerLogging $logger,
        Queues $queues
    ) {
        $this->peerId = $peerId;
        $this->broker = $broker;
        $this->logger = $logger;
        $this->queues = $queues;

        $this->publishChannel = $broker->channel();
        $this->consumeChannel = $broker->channel();
        $this->handlers = [];
    }

    /**
     * Listen begins listening for command requests made by an invoker.
     *
     * @return bool True If server starterd listening in a new namespace.
     *
     * @throws ?
     */
    public function listen(string $namespace, callable $handler): bool
    {
        // we're already listening, just swap the handler
        if (array_key_exists($namespace, $this->handlers)) {
            $this->handlers[$namespace] = $handler;

            return false;
        }

        $this->consumeChannel->queueBind(
            $this->queues->requestQueue($this->peerId), // $queue = ''
            Exchanges::multicastExchange,               // $exchange
            $namespace                                  // $routingKey = ''
        );

        $queue = $this->queues->get($this->consumeChannel, $namespace);

        $this->consumeChannel->consume(
            function(BunnyMessage $message, Channel $channel, Client $bunny) {
                $this->dispatch($message, $channel);
            },
            $queue,     // $queue
            $queue      // $consumerTag
        );

        $this->handlers[$namespace] = $handler;

        return true;
    }

    /**
     * Unlisten stops listening for command requests made by an invoker.
     *
     * @return bool True If server stopped listening in $namespace.
     *
     * @throws ?
     */
    public function unlisten($namespace): bool
    {
        if (!array_key_exists($namespace, $this->handlers)) {
            return false;
        }

        $this->consumeChannel->queueUnbind(
            $this->queues->requestQueue($this->peerId), // $queue = ''
            Exchanges::multicastExchange,               // $exchange,
            $namespace                                  // $routingKey = ''
        );

        // balanced queue
        $this->consumeChannel->cancel($this->queues->balancedRequestQueue($namespace));

        unset($this->handlers[$namespace]);

        return true;
    }

    // Do we really need a handler? go acts differently so maybe.
    public function initialize()
    {
        $this->consumeChannel->qos(
            0,  // $prefetchSize = 0,
            1   // $prefetchCount = 0
        );

        // TODO:
        // s.consumeChannel.NotifyClose(s.amqpClosed)

        $queue = $this->queues->requestQueue($this->peerId);

        $this->consumeChannel->queueDeclare(
            $queue,
            false,  // passive
            false,  // durable
            true,   // exclusive,
            false,  // autoDelete
            false,  // noWait
            ['x-max-priority' => 3] // TODO: need proper priorities
        );

        $this->consumeChannel->queueBind(
            $queue,
            Exchanges::unicastExchange,
            $this->peerId
        );

        $this->consumeChannel->consume(
            function(BunnyMessage $message, Channel $channel, Client $bunny) {
                $this->dispatch($message, $channel);
            },
            $queue,
            $queue, // use queue name as consumer tag
            false,  // no local
            false,  // autoAck
            true    // exclusive
        );

        $this->logger->logServerStart($this->peerId, 1);
    }

    public function stop()
    {
        $this->logger->logServerStopping($this->peerId, 0);

        foreach ($this->handlers as $namespace => $handler) {
            $this->unlisten($namespace);
        }

        $this->consumeChannel->close();

        $this->logger->logServerStop($this->peerId);
    }

    /**
     * Dispatch validates an incoming command request and dispatches it the
     * appropriate handler.
     */
    private function dispatch(BunnyMessage $message, Channel $channel)
    {
        $messageId = MessageId::createFromString(
            $message->getHeader('message-id')
        );

        if (null === $messageId) {
            $channel->nack($message, false, false); // don't requeue
            $this->logger->logServerInvalidMessageID($this->peerId, $messageId);
            return;
        }

        $namespace = $message->getHeader(Message::namespaceHeader);
        $command = $message->getHeader(Message::commandHeader);
        if (null === $namespace) {
            $channel->nack($message, false, false); // don't requeue
            $this->logger->logIgnoredMessage(
                $this->peerId,
                $messageId,
                'Namespace header is not a string.'
            );

            return;
        }
        if (null === $command) {
            $channel->nack($message, false, false); // don't requeue
            $this->logger->logIgnoredMessage(
                $this->peerId,
                $messageId,
                'Command header is not a string.'
            );

            return;
        }

        if (!array_key_exists($namespace, $this->handlers)) {
            $channel->nack(
                $message,
                false,
                $message->exchange === Exchange::balancedExchange // requeue if "balanced"
            );
            $this->logger->logNoLongerListening($peerId, $messageId, $namespace);
            return;
        }

        $handler = $this->handlers[$namespace];

        // TODO:
        // find the source session revision
        // source, err := s.revisions.GetRevision(msgID.Ref)
        // if err != nil {
        // _ = msg.Reject(false) // false = don't requeue
        // logIgnoredMessage(s.logger, s.peerID, msgID, err)
        // return
        // }
        //
        $this->handle(
            $messageId,
            $message,
            $channel,
            $namespace,
            $command,
            null, // TODO: get $source,
            $handler
        );
    }

    /**
     * handle invokes the command handler for request.
     */
    private function handle(
        MessageId $messageId,
        BunnyMessage $message,
        Channel $channel,
        string $namespace,
        string $command,
        $source, //TODO: fix this -> Revision $source,
        callable $handler
    ) {
        // TODO: this is mocked up - this is not how rinq-go does it.
        $context = Context::create(0, $message->getHeader('message-id'));

        $request = Request::create(
            $source,
            $namespace,
            $command,
            CBOREncoder::decode($message->content),
            $message->exchange === Exchanges::multicastExchange
        );

        $response = new Response(
            $context,
            $this->publishChannel,
            $messageId,
            $request,
            $message->getHeader('reply-to')
        );

        $this->logger->logRequestBegin($context, $this->peerId, $messageId, $request);

        try {
            $handler($context, $request, $response);
            // TODO: We need some kinda of timeout check.
            $channel->ack($message);
            $this->logger->logRequestEnd($context, $this->peerId, $messageId, $request, $response->payload);
        } catch (\Exception $e) {
            $channel->nack($message);
            $this->logger->logRequestEnd($context, $this->peerId, $messageId, $request, $response->payload, $e->getMessage());

            throw $e;
        }
        // TODO
            // if (finalize() {
        //     _ = msg.Ack(false) // false = single message
        //
        //     if dr, ok := res.(*debugResponse); ok {
        //     defer dr.Payload.Close()
        //     logRequestEnd(ctx, s.logger, s.peerID, msgID, req, dr.Payload, dr.Err)
        //     }
        //     } else if msg.Exchange == balancedExchange {
        //     select {
        //     case <-ctx.Done():
        //     _ = msg.Reject(false) // false = don't requeue
        //     logRequestRejected(ctx, s.logger, s.peerID, msgID, req, ctx.Err().Error())
        //     default:
        //     _ = msg.Reject(true) // true = requeue
        //     logRequestRequeued(ctx, s.logger, s.peerID, msgID, req)
        //     }
        //     } else {
        //     _ = msg.Reject(false) // false = don't requeue
        //     logRequestRejected(ctx, s.logger, s.peerID, msgID, req, ctx.Err().Error())
        //     }
    }

    private $peerId;
    private $broker;
    private $logger;
    private $queues;

    private $publishChannel;
    private $consumeChannel;
    private $handlers;
}
