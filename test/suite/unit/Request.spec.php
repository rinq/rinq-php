<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use function Eloquent\Phony\mock;

describe(Request::class, function () {
    describe('->construct()', function () {
        it('constructs a new request', function () {
            $source = mock(Revision::class)->get();
            $subject = Request::create(
                $source,
                '<namespace>',
                '<command>',
                '<payload>',
                true
            );

            expect($subject->source())->to->equal($source);
            expect($subject->namespace())->to->equal('<namespace>');
            expect($subject->command())->to->equal('<command>');
            expect($subject->payload())->to->equal('<payload>');
            expect($subject->isMulticast())->to->be->true();
        });
    });
});
