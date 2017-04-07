<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message as BunnyMessage;
use Rinq\Revision;
use Rinq\Ident\PeerId;
use Rinq\Internal\Command\Server as ServerInterface;

/**
 * Server processes command requests made by an invoker.
 */
class Server implements ServerInterface
{
    public function __construct(
        PeerId $peerId,
        Channel $channel,
        ServerLogging $logger,
        Queues $queues
    ) {
        $this->peerId = $peerId;
        $this->channel = $channel;
        $this->logger = $logger;
        $this->queues = $queues;
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

        $this->channel->queueBind(
            $this->queues->requestQueue($this->peerId), // $queue = ''
            Exchanges::multicastExchange,               // $exchange
            $namespace                                  // $routingKey = ''
        );

        $queue = $this->queues->get($this->channel, $namespace);

        $this->channel->consume(
            function(BunnyMessage $message, Channel $channel, Client $bunny) {
                $this->dispatch($message);
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

        $this->channel->queueUnbind(
            $this->queues->requestQueue($this->peerId), // $queue = ''
            Exchanges::multicastExchange,               // $exchange,
            $namespace                                  // $routingKey = ''
        );

        // balanced queue
        $this->channel->cancel($this->queues->balancedRequestQueue($namespace));

        unset($this->handlers[$namespace]);

        return true;
    }

    // Do we really need a handler? go acts differently so maybe.
    public function initialize()
    {
        $this->channel->qos(
            0,  // $prefetchSize = 0,
            1   // $prefetchCount = 0
        );

        // TODO:
        // s.channel.NotifyClose(s.amqpClosed)

        $queue = $this->queues->requestQueue($this->peerId);

        $this->channel->queueDeclare(
            $queue,
            false,  // passive
            false,  // durable
            true,   // exclusive,
            false,  // autoDelete
            false,  // noWait
            ['x-max-priority' => 3] // TODO: need proper priorities
        );

        $this->channel->queueBind(
            $queue,
            Exchanges::unicastExchange,
            $this->peerId
        );

        $this->channel->consume(
            function(BunnyMessage $message, Channel $channel, Client $bunny) {
                $this->dispatch($message);
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
        $this->logger->logServerStop($this->peerId);
    }

    /**
     * Dispatch validates an incoming command request and dispatches it the
     * appropriate handler.
     */
    private function dispatch(BunnyMessage $message)
    {
        // TODO: Parse as Ident\MessageId
        $messageId = $message->getHeader('message-id');

        if (null === $messageId) {
            $this->channel->nack($message, false, false); // don't requeue
            $this->logger->logServerInvalidMessageID($this->peerId, $messageId);
            return;
        }

        $namespace = $message->getHeader(Message::namespaceHeader);
        $command = $message->getHeader(Message::commandHeader);
        if (null === $namespace) {
            $this->channel->nack($message, false, false); // don't requeue
            $this->logger->logIgnoredMessage(
                $this->peerId,
                $messageId,
                'Namespace header is not a string.'
            );

            return;
        }
        if (null === $command) {
            $this->channel->nack($message, false, false); // don't requeue
            $this->logger->logIgnoredMessage(
                $this->peerId,
                $messageId,
                'Command header is not a string.'
            );

            return;
        }

        if (!array_key_exists($namespace, $this->handlers)) {
            $this->channel->nack(
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
            $namespace,
            $command,
            null, // TODO: get $source,
            $handler
        );
    }

    // handle invokes the command handler for request.
    private function handle(
        string $messageId,
        BunnyMessage $message,
        string $namespace,
        string $command,
        Revision $source,
        callable $handler
    ) {
    //     ctx := amqputil.UnpackTrace(s.parentCtx, msg)
    //     ctx, cancel := amqputil.UnpackDeadline(ctx, msg)
    //     defer cancel()
    //
    //     req := rinq.Request{
    //     Source:      source,
    //     Namespace:   ns,
    //     Command:     cmd,
    //     Payload:     rinq.NewPayloadFromBytes(msg.Body),
    //     IsMulticast: msg.Exchange == multicastExchange,
    //     }
    //
    //     res, finalize := newResponse(
    //     ctx,
    //     s.channels,
    //     msgID,
    //     req,
    //     unpackReplyMode(msg),
    //     )
    //
    //     if s.logger.IsDebug() {
    //     res = newDebugResponse(res)
    //     logRequestBegin(ctx, s.logger, s.peerID, msgID, req)
    //     }
    //
        $handler('ctx', 'req', 'res');

        
        var_dump($response);
    //
    //     if finalize() {
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
    private $channel;
    private $logger;
    private $handlers;
}
