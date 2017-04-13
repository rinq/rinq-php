<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

describe(Context::class, function () {
    describe('->construct()', function () {
        it('constructs a new context', function () {
            $subject = Context::create(1.234, '<trace-id>');

            expect($subject->timeout())->to->equal(1.234);
            expect($subject->traceId())->to->equal('<trace-id>');
        });

        it('constructs a new context without a trace-id', function () {
            $subject = Context::create(1.234);

            expect($subject->timeout())->to->equal(1.234);
            expect($subject->traceId())->to->be->null();;
        });
    });
});
