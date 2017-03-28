<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Rinq\Ident\PeerId;
use Rinq\Internal\Command\Server as ServerInterface;

class Queues implements ServerInterface
{
    /**
     * balancedRequestQueue returns the name of the queue used for balanced
     * command requests in the given namespace.
     */
    public function balancedRequestQueue(string $namespace): string
    {
        return 'cmd.' + $namespace;
    }

    /**
     * requestQueue returns the name of the queue used for unicast and multicast
     * command requests.
     */
    public function requestQueue(PeerId $peerId): string
    {
        return $peerId . ShortString() . '.req';
    }

    /**
     * responseQueue returns the name of the queue used for command responses.
     */
    public function responseQueue(PeerId $peerId): string
    {
        return $peerId . ShortString() . '.rsp';
    }

    /**
     * Get declares the AMQP queue used for balanced command requests in the given
     * namespace and returns the queue name.
     */
    private function get(Channel $channel, string $namespace): string
    {
        if (array_key_exists($namespace, $this->queues)) {
            return $this->queues[$namespace];
        }

        $queue = 'cmd.' . $namespace;

        $channel->queueDeclare(
            $queue,
            false, // $passive
            true,  // durable
            false, // exclusive,
            false, // autoDelete
            false, // noWait
            ['x-max-priority' => 10] // TODO: need proper priorities
        );

        $channel . queueBind(
            $queue,
            'cmd.bal',
            $namespace
        );

        $this->queues[$namespace] = $queue;

        return $queue;
    }

    /**
     * @var array Map of queues to namespae
     */
    private $queues = [];
}
