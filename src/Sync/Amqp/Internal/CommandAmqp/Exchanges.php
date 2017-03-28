<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Rinq\Internal\Command\Server as ServerInterface;

class Exchanges implements ServerInterface
{
    /**
     * unicastExchange is the exchange used to publish internal command requests
     * directly to a specific peer.
     */
    const unicastExchange = 'cmd.uc';

    /**
     * multicastExchange is the exchange used to publish comman requests to the
     * all peers that can service the namespace.
     */
    const multicastExchange = 'cmd.mc';

    /**
     * balancedExchange is the exchange used publish command requests to the
     * first available peer that can service the namespace.
     */
    const balancedExchange = 'cmd.bal';

    /**
     * responseExchange is the exchange used to publish command responses.
     */
    const responseExchange = 'cmd.rsp';

    /**
     * Declare the exchanges.
     */
    public function declareExchanges(Channel $channel)
    {
        $channel->exchangeDeclare(self::unicastExchange, 'direct');
        $channel->exchangeDeclare(self::multicastExchange, 'direct');
        $channel->exchangeDeclare(self::balancedExchange, 'direct');
        $channel->exchangeDeclare(self::responseExchange, 'topic');
    }
}
