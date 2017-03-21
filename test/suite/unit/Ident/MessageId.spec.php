<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

describe(MessageId::class, function () {
    beforeEach(function () {
        $this->reference = Reference::create(SessionId::create(PeerId::create(123456789, 111), 222), 333);
        $this->subject = MessageId::create($this->reference, 444);
    });

    describe('->construct()', function () {
        it('should construct a new message id', function () {
            expect($this->subject->reference)->to->equal($this->reference);
            expect($this->subject->sequence)->to->equal(444);
        });
    });

    describe('->toString()', function () {
        it('should have a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222@333#444');
        });
    });
});
