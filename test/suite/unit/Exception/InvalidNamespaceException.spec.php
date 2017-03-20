<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

describe('InvalidNamespaceException', function () {
    describe('::emptyNamespace()', function () {
        it('should construct a message about an empty namespace', function () {
            expect(InvalidNamespaceException::emptyNamespace()->getMessage())
                ->to->equal('Namespace must not be empty.');
        });
    });
    describe('::reservedNamespace()', function () {
        it('should construct a message about the use of a reserved keyword', function () {
            expect(InvalidNamespaceException::reservedNamespace('foo')->getMessage())
                ->to->equal('Namespace \'foo\' is reserved.');
        });
    });
    describe('::invalidCharacters()', function () {
        it('should construct a message about a namespace with invalid characters', function () {
            expect(InvalidNamespaceException::invalidCharacters('foo&@#')->getMessage())
                ->to->equal('Namespace \'foo&@#\' contains invalid characters.');
        });
    });
});
