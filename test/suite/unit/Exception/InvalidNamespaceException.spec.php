<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

describe(InvalidNamespaceException::class, function () {
    describe('::emptyNamespace()', function () {
        it('constructs a message regarding an empty namespace', function () {
            expect(InvalidNamespaceException::emptyNamespace()->getMessage())
                ->to->equal('Namespace must not be empty.');
        });
    });
    describe('::reservedNamespace()', function () {
        it('constructs a message about the use of a reserved keyword', function () {
            expect(InvalidNamespaceException::reservedNamespace('foo')->getMessage())
                ->to->equal('Namespace \'foo\' is reserved.');
        });
    });
    describe('::invalidCharacters()', function () {
        it('constructs a message regarding a namespace with invalid characters', function () {
            expect(InvalidNamespaceException::invalidCharacters('foo&@#')->getMessage())
                ->to->equal('Namespace \'foo&@#\' contains invalid characters.');
        });
    });
});
