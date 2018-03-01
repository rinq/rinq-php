<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use RuntimeException;
use Rinq\Ident\PeerId;
use Rinq\Ident\Reference;
use Rinq\Ident\SessionId;

describe(ErrorException::class, function () {
    describe('__construct()', function () {
        it('constructs an exception with a sensible default message', function () {
            $subject = new ErrorException('<type>');
            expect($subject->type())->to->equal('<type>');
            expect($subject->getMessage())->to->equal('Unexpected error exception.');
        });

        it('constructs an exception with supplied message', function () {
            $subject = new ErrorException('<type>', '<message>');
            expect($subject->type())->to->equal('<type>');
            expect($subject->getMessage())->to->equal('<message>');
        });

        it('throws when type is empty', function () {
            expect(function () {
                $subject = new ErrorException('');
            })->to->throw(RuntimeException::class, 'Error type cannot be empty.');
        });
    });
});
