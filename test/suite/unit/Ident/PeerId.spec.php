<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

describe(PeerId::class, function () {
    beforeEach(function () {
        $this->subject = PeerId::create(123456789, 111);
    });

    describe('::create()', function () {
        it('constructs a new peer id', function () {
            expect($this->subject->clock)->to->equal(123456789);
            expect($this->subject->rand)->to->equal(111);
        });
    });

    describe('::createFromString()', function () {
        it('constructs a new peer id from a string', function () {
            $subject = PeerId::createFromString('75BCD15-006F');

            expect($subject->clock)->to->equal(123456789);
            expect($subject->rand)->to->equal(111);
        });

        it('throws with invalid peer id delimiter', function () {
            expect(function () {
                $subject = PeerId::createFromString('75BCD15.006F');
            })->to->throw(RuntimeException::class, 'Peer ID 75BCD15.006F is invalid.');
        });

        it('throws with no peer id delimiter', function () {
            expect(function () {
                $subject = PeerId::createFromString('75BCD15006F');
            })->to->throw(RuntimeException::class, 'Peer ID 75BCD15006F is invalid.');
        });
    });

    describe('->shortString()', function () {
        it('returns a meaningful shortened string representation', function () {
            expect(strval($this->subject->shortString()))->to->equal('006F');
        });
    });

    describe('->__toString()', function () {
        it('returns a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F');
        });
    });
});
