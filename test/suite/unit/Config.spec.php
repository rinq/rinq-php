<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Psr\Log\LoggerInterface;
use function Eloquent\Phony\mock;

describe(Config::class, function () {
    describe('->construct()', function () {
        it('should construct new config', function () {
            $logger = mock(LoggerInterface::class)->get();
            $subject = Config::create(
                1.234, // $defaultTimeout
                $logger,
                111, // $commandWorkers,
                222, // $sessionWorkers,
                333 // $pruneInterval
            );

            expect($subject->defaultTimeout())->to->equal(1.234);
            expect($subject->logger())->to->equal($logger);
            expect($subject->commandWorkers())->to->equal(111);
            expect($subject->sessionWorkers())->to->equal(222);
            expect($subject->pruneInterval())->to->equal(333);
        });
    });
});
