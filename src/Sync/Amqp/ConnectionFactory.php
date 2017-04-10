<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Bunny\Client;
use Bunny\Constants;
use Bunny\Message;
use Bunny\Channel;
use Bunny\Exception\ClientException;
use Rinq\Ident\PeerId;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Exchanges;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Invoker;
use Rinq\Sync\Amqp\Internal\CommandAmqp\InvokerLogging;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Queues;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Server;
use Rinq\Sync\Amqp\Internal\CommandAmqp\ServerLogging;
use Rinq\Sync\Config;

/**
 * Connects to an AMQP-based Rinq network, establishing the peer's unique
 * identity on the network.
 */
final class ConnectionFactory
{
    public static function create(Config $config, string $dsn = ''): self
    {
        return new self($config, $dsn);
    }

    /**
     * @throws ClientException When failing to connect to AMQP.
     */
    public function connect()
    {
        $this->broker->connect();

        $channel = $this->broker->channel(); // publishing channel
        $peerId = $this->establishIdentity($channel);
        $logger = $this->config->logger();

        $queues = new Queues();
        Exchanges::declareExchanges($channel);

        $invoker = new Invoker(
            $peerId,
            $queues,
            $this->broker,
            new InvokerLogging($logger),
            $this->config->defaultTimeout()
        );
        $invoker->initialize();

        $server = new Server(
            $peerId,
            $this->broker,
            new ServerLogging($logger),
            $queues
        );
        $server->initialize();

        return Peer::create(
            $peerId,
            $this->broker,
            // localStore,
            // remoteStore,
            $invoker,
            $server,
            // notifier,
            // listener,
            new Logging($logger)
        );
    }

    /**
     * Allocate a new peer ID on the broker.
     */
    private function establishIdentity(Channel $channel): PeerId
    {
        while (true) {
            try {
                $peerId = PeerId::create(time(), rand(1, 0xFFFF));

                $channel->queueDeclare(
                    $peerId,    // queue
                    false,      // passive
                    false,      // durable
                    true        // exclusive
                );

                $this->config->logger()->debug(
                    sprintf(
                        '%s connected to \'%s\' as %s.',
                        $peerId->shortString(),
                        $this->dsn,
                        $peerId
                    )
                );

                return $peerId;
            } catch (ClientException $e) {
                if (Constants::STATUS_RESOURCE_LOCKED !== $e->getCode()) {
                    throw $e;
                }

                $this->config->logger()->debug(
                    sprintf(
                        '%s already registered, retrying with a different peer ID',
                        $peerId
                    )
                );
            }
        }
    }

    private function __construct(Config $config, string $dsn)
    {
        if (empty($dsn)) {
            $dsn = 'localhost';
        }

        $this->config = $config;
        $this->dsn = $dsn;
        $this->broker = new Client(['host' => $dsn]);
    }

    private $config;
    private $dsn;
    private $broker;
}
