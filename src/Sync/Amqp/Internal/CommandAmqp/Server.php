<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
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
            $handler,
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
    public function initialize(callable $handler)
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
            $handler,
            $queue,
            $queue, // use queue name as consumer tag
            false,  // no local
            false,  // autoAck
            true    // exclusive
        );
    }

    public function run()
    {
        $this->logger->logServerStart($this->peerId, 1);
    }

    public function stop()
    {
        $this->logger->logServerStopping($this->peerId, 0);
        $this->logger->logServerStop($this->peerId);
    }

    private $peerId;
    private $channel;
    private $logger;
    private $handlers;
}
