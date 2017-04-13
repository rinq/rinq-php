<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

describe(Attribute::class, function () {
    describe('->construct()', function () {
        it('constructs a non-frozen attribute', function () {
            $subject = Attribute::create('foo', '{"bar":"baz"}');

            expect($subject->key())->to->equal('foo');
            expect($subject->value())->to->equal('{"bar":"baz"}');
            expect($subject->isFrozen())->to->be->false();
        });

        it('constructs a frozen attribute', function () {
            $subject = Attribute::freeze('foo', '{"bar":"baz"}');

            expect($subject->key())->to->equal('foo');
            expect($subject->value())->to->equal('{"bar":"baz"}');
            expect($subject->isFrozen())->to->be->true();
        });
    });
});
