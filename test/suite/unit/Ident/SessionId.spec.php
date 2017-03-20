<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

describe(SessionId::class, function () {
    beforeEach(function () {
        $this->peerId = PeerId::create(123456789, 111);
        $this->subject = SessionId::create($this->peerId, 222);
    });

    describe('->construct()', function () {
        it('should construct a new session id', function () {
            expect($this->subject->peerId)->to->equal($this->peerId);
            expect($this->subject->sequence)->to->equal(222);
        });
    });

    describe('->toString()', function () {
        it('should have a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222');
        });
    });
});
