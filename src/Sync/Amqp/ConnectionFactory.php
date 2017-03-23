<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Bunny\Client;
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

    private function __construct(Config $config, string $dsn)
    {
        if (empty($dsn)) {
            $dsn = 'localhost';
        }

        $broker = (new Client(['host' => $dsn]))->connect();

        $this->config = $config;
    }

    private $config;
}
