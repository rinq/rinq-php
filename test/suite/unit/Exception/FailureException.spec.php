<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;
use Rinq\Ident\PeerId;
use Rinq\Ident\Reference;
use Rinq\Ident\SessionId;

describe(FailureException::class, function () {
    describe('__construct()', function () {
        it('constructs an exception with a sensible default message', function () {
            $subject = new FailureException('<type>');
            expect($subject->type())->to->equal('<type>');
            expect($subject->getMessage())->to->equal('Unknown failure.');
            expect($subject->payload())->to->be->null();
        });

        it('constructs an exception with supplied message', function () {
            $subject = new FailureException('<type>', '<message>');
            expect($subject->type())->to->equal('<type>');
            expect($subject->getMessage())->to->equal('<message>');
            expect($subject->payload())->to->be->null();
        });

        it('constructs an exception with supplied message and payload', function () {
            $subject = new FailureException('<type>', '<message>', ['foo' => 'bar']);
            expect($subject->type())->to->equal('<type>');
            expect($subject->getMessage())->to->equal('<message>');
            expect($subject->payload())->to->equal(['foo' => 'bar']);
        });

        it('throws when type is empty', function () {
            expect(function () {
                $subject = new FailureException('');
            })->to->throw(RuntimeException::class, 'Failure type cannot be empty.');
        });
    });
});
