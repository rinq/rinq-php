<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

describe(Reference::class, function () {
    beforeEach(function () {
        $this->sessionId = SessionId::create(PeerId::create(123456789, 111), 222);
        $this->subject = Reference::create($this->sessionId, 333);
    });

    describe('->construct()', function () {
        it('should construct a new reference', function () {
            expect($this->subject->sessionId)->to->equal($this->sessionId);
            expect($this->subject->revision)->to->equal(333);
        });
    });

    describe('->toString()', function () {
        it('should have a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222@333');
        });
    });
});
