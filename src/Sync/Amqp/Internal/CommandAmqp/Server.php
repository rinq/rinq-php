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
        LoggerInterface $logger,
        Queues $queues = null
    ) {
        if (null === $queues) {
            $queues = new Queues();
        }

        $this->peerId = $peerId;
        $this->channel = $channel;
        $this->logger = $logger;
        $this->queues = $queues;
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

        $queue = $this->queues->get($namespace);

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

    private $peerId;
    private $channel;
    private $logger;
    private $handlers;
}
