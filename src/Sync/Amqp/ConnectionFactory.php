<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Bunny\Client;
use Bunny\Constants;
use Bunny\Exception\ClientException;
use Rinq\Ident\PeerId;
use Rinq\Sync\Config;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Queues;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Server;
use Rinq\Sync\Amqp\Internal\CommandAmqp\InvokerLogging;
use Rinq\Sync\Amqp\Internal\CommandAmqp\ServerLogging;
use Rinq\Sync\Amqp\Internal\CommandAmqp\Invoker;

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

        $peerId = $this->establishIdentity();
        $queues = new Queues();
        $channel = $this->broker->channel();

        $invoker = new Invoker(
            $peerId,
            $queues,
            $channel,
            new InvokerLogging($this->config->logger()),
            $this->config->defaultTimeout()
        );

        $invoker->initialize(function (Message $message, Channel $channel, Client $bunny) {
            var_dump($message);
        });
        $invoker->run(); // TODO: run not done yet.

        $server = new Server(
            $peerId,
            $this->broker->channel(),
            new ServerLogging($this->config->logger()),
            $queues
        );
        $server->run(); // TODO: run not done yet.

        $server->initialize(function (Message $message, Channel $channel, Client $bunny) {
            var_dump($message);
        });

        return Peer::create(
            $peerId,
            $this->broker,
        //     localStore,
        //     remoteStore,
            $invoker,
            $server,
        //     notifier,
        //     listener,
            $this->config->logger()
        );
    }

    /**
     * Allocate a new peer ID on the broker.
     */
    private function establishIdentity(): PeerId
    {
        while (true) {
            try {
                $peerId = PeerId::create(time(), rand(1, 0xFFFF));

                $this->broker->channel()->queueDeclare(
                    $peerId,    // queue
                    false,      // passive
                    false,      // durable
                    true        // exclusive
                );

                $this->config->logger()->info(
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

                $this->config->logger()->info(
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
