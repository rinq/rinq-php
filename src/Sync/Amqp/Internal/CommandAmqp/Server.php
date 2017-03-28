<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Psr\Log\LoggerInterface;
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
        LoggerInterface $logger
    ) {
        $this->peerId = $peerId;
        $this->channel = $channel;
        $this->logger = $logger;

        $this->handlers = [];
        $this->queues = [];
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
            $this->peerId->shortString() . '.req',  // $queue = ''
            'cmd.mc',                               // $exchange
            $namespace                              // $routingKey = ''
        );

        $queue = $this->getQueue($namespace);

        $this->channel->consume(
            $handler,
            $queue,     // $queue
            $queue,     // $consumerTag
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
            $this->peerId->shortString() . '.req',  // $queue = ''
            'cmd.mc',                               // $exchange,
            $namespace                              // $routingKey = ''
        );

        // balanced queue
        $this->channel->cancel('cmd.' . $namespace);

        unset($this->handlers[$namespace]);

        return true;
    }

    private function getQueue(Channel $channel, string $namespace)
    {
        if (array_key_exists($namespace, $this->queues) {
            return $this->queues[$namespace];
        }

        $queue = 'cmd.' . $namespace;

        $channel.queueDeclare(
            $queue,
            false, // $passive
            true,  // durable
            false, // exclusive,
            false, // autoDelete
            false, // noWait
            // amqp.Table{"x-max-priority": priorityCount}, // TODO: priorities??
        );

        $channel.queueBind(
            $queue,
            'cmd.bal',
            $namespace
        );

        $this->queues[$namespace] = $queue

        return $queue;
    }

    private $peerId;
    private $channel;
    private $logger;
    private $handlers;
    private $queues;

}
