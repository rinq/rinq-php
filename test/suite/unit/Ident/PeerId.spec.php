<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

describe(PeerId::class, function () {
    beforeEach(function () {
        $this->subject = PeerId::create(123456789, 111);
    });

    describe('->construct()', function () {
        it('should construct a new peer id', function () {
            expect($this->subject->clock)->to->equal(123456789);
            expect($this->subject->rand)->to->equal(111);
        });
    });

    describe('->toString()', function () {
        it('should have a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F');
        });
    });
});
