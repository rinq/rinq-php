<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

describe(SessionId::class, function () {
    beforeEach(function () {
        $this->peerId = PeerId::create(123456789, 111);
        $this->subject = SessionId::create($this->peerId, 222);
    });

    describe('::create()', function () {
        it('constructs a new session id', function () {
            expect($this->subject->peerId)->to->equal($this->peerId);
            expect($this->subject->sequence)->to->equal(222);
        });
    });

    describe('::createFromString()', function () {
        it('constructs a new session id from a string', function () {
            $subject = SessionId::createFromString('75BCD15-006F.222');

            expect(strval($subject->peerId))->to->equal('75BCD15-006F');
            expect($subject->sequence)->to->equal(222);
        });

        it('throws with invalid session id delimiter', function () {
            expect(function () {
                $subject = SessionId::createFromString('75BCD15-006F-222');
            })->to->throw(RuntimeException::class, 'Session ID 75BCD15-006F-222 is invalid.');
        });

        it('throws with no session id delimiter', function () {
            expect(function () {
                $subject = SessionId::createFromString('75BCD15-006F222');
            })->to->throw(RuntimeException::class, 'Session ID 75BCD15-006F222 is invalid.');
        });
    });

    describe('->shortString()', function () {
        it('returns a meaningful shortened string representation', function () {
            expect(strval($this->subject->shortString()))->to->equal('006F.222');
        });
    });

    describe('->toString()', function () {
        it('returns a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222');
        });
    });
});
