<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

describe(Reference::class, function () {
    beforeEach(function () {
        $this->sessionId = SessionId::create(PeerId::create(123456789, 111), 222);
        $this->subject = Reference::create($this->sessionId, 333);
    });

    describe('::create()', function () {
        it('constructs a new reference', function () {
            expect($this->subject->sessionId)->to->equal($this->sessionId);
            expect($this->subject->revision)->to->equal(333);
        });
    });

    describe('::createFromString()', function () {
        it('constructs a new reference from a string', function () {
            $subject = Reference::createFromString('75BCD15-006F.222@333');

            expect(strval($subject->sessionId))->to->equal('75BCD15-006F.222');
            expect($subject->revision)->to->equal(333);
        });

        it('throws with invalid reference delimiter', function () {
            expect(function () {
                $subject = Reference::createFromString('75BCD15-006F.222.333');
            })->to->throw(RuntimeException::class, 'Reference 75BCD15-006F.222.333 is invalid.');
        });

        it('throws with no reference delimiter', function () {
            expect(function () {
                $subject = Reference::createFromString('75BCD15-006F.222333');
            })->to->throw(RuntimeException::class, 'Reference 75BCD15-006F.222333 is invalid.');
        });
    });

    describe('->shortString()', function () {
        it('returns a meaningful shortened string representation', function () {
            expect(strval($this->subject->shortString()))->to->equal('006F.222@333');
        });
    });

    describe('->__toString()', function () {
        it('returns a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222@333');
        });
    });
});
