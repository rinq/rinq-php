<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use function Eloquent\Phony\mock;
use Bunny\Channel;

describe(Exchanges::class, function () {
    describe('::declareExchanges()', function () {
        it('declares the set of exchanges', function () {
            $channel = mock(Channel::class);
            Exchanges::declareExchanges($channel->get());

            $channel->exchangeDeclare->calledWith('cmd.uc', 'direct');
            $channel->exchangeDeclare->calledWith('cmd.mc', 'direct');
            $channel->exchangeDeclare->calledWith('cmd.bal', 'direct');
            $channel->exchangeDeclare->calledWith('cmd.rsp', 'topic');
        });
    });
});
