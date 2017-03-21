<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

describe(Context::class, function () {
    describe('->construct()', function () {
        it('should construct new context', function () {
            $subject = Context::create(1.234, '<trace-id>');

            expect($subject->timeout())->to->equal(1.234);
            expect($subject->traceId())->to->equal('<trace-id>');
        });
    });
});
