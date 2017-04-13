<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Ident;

use RuntimeException;

describe(MessageId::class, function () {
    beforeEach(function () {
        $this->reference = Reference::create(SessionId::create(PeerId::create(123456789, 111), 222), 333);
        $this->subject = MessageId::create($this->reference, 444);
    });

    describe('::create()', function () {
        it('constructs a new message id', function () {
            expect($this->subject->reference)->to->equal($this->reference);
            expect($this->subject->sequence)->to->equal(444);
        });
    });

    describe('::createFromString()', function () {
        it('constructs a new message id from a string', function () {
            $subject = MessageId::createFromString('75BCD15-006F.222@333#444');

            expect(strval($subject->reference))->to->equal('75BCD15-006F.222@333');
            expect($subject->sequence)->to->equal(444);
        });

        it('throws with invalid peer id delimiter', function () {
            expect(function () {
                $subject = MessageId::createFromString('75BCD15-006F.222@333@444');
            })->to->throw(RuntimeException::class, 'Message ID 75BCD15-006F.222@333@444 is invalid.');
        });

        it('throws with no peer id delimiter', function () {
            expect(function () {
                $subject = MessageId::createFromString('75BCD15-006F.222@333444');
            })->to->throw(RuntimeException::class, 'Message ID 75BCD15-006F.222@333444 is invalid.');
        });
    });

    describe('->shortString()', function () {
        it('returns a meaningful shortend string representation', function () {
            expect(strval($this->subject->shortString()))->to->equal('006F.222@333#444');
        });
    });

    describe('->__toString()', function () {
        it('returns a meaningful string representation', function () {
            expect(strval($this->subject))->to->equal('75BCD15-006F.222@333#444');
        });
    });
});
