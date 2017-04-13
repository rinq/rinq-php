<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Async;

use Psr\Log\LoggerInterface;
use function Eloquent\Phony\mock;

describe(Config::class, function () {
    describe('->construct()', function () {
        it('constructs a new config with sensible defaults', function () {
            $logger = mock(LoggerInterface::class)->get();
            $subject = Config::create($logger);

            expect($subject->logger())->to->equal($logger);
            expect($subject->defaultTimeout())->to->equal(5.0);
            expect($subject->pruneInterval())->to->equal(180.0);
            expect($subject->commandWorkers())->to->equal(0);
            expect($subject->sessionWorkers())->to->equal(0);
        });

        it('constructs a new config with specified values', function () {
            $logger = mock(LoggerInterface::class)->get();
            $subject = Config::create(
                $logger,
                1.111,  // $defaultTimeout
                2.222,  // $pruneInterval
                333,    // $commandWorkers
                444     // $sessionWorkers
            );

            expect($subject->logger())->to->equal($logger);
            expect($subject->defaultTimeout())->to->equal(1.111);
            expect($subject->pruneInterval())->to->equal(2.222);
            expect($subject->commandWorkers())->to->equal(333);
            expect($subject->sessionWorkers())->to->equal(444);
        });
    });
});
