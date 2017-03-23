<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync;

use Psr\Log\LoggerInterface;
use function Eloquent\Phony\mock;

describe(Config::class, function () {
    describe('->construct()', function () {
        it('should construct new config with sensible defaults', function () {
            $logger = mock(LoggerInterface::class)->get();
            $subject = Config::create($logger);

            expect($subject->logger())->to->equal($logger);
            expect($subject->defaultTimeout())->to->equal(5.0);
            expect($subject->pruneInterval())->to->equal(180.0);
        });

        it('should construct new config with specified values', function () {
            $logger = mock(LoggerInterface::class)->get();
            $subject = Config::create(
                $logger,
                1.111,  // $defaultTimeout
                2.222   // $pruneInterval
            );

            expect($subject->logger())->to->equal($logger);
            expect($subject->defaultTimeout())->to->equal(1.111);
            expect($subject->pruneInterval())->to->equal(2.222);
        });
    });
});
