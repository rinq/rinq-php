<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

describe(AttributeTable::class, function () {
    describe('->construct()', function () {
        it('should construct new attribute table', function () {
            $subject = AttributeTable::create(['foo' => 'bar', 'baz' => 'qux']);

            expect($subject->attributes())->to->equal(['foo' => 'bar', 'baz' => 'qux']);
        });
    });
});
