<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\PeerId;
use Rinq\Ident\Reference;
use Rinq\Ident\SessionId;

describe(StaleFetchException::class, function () {
    beforeEach(function () {
        $this->reference = Reference::create(SessionId::create(PeerId::create(111, 222), 333), 444);
        $this->subject = new StaleFetchException($this->reference);
    });

    describe('->construct()', function () {
        it('constructs a meaningful exception message', function () {
            expect($this->subject->getMessage())->to->equal(
                'Can not fetch attributes at 6F-00DE.333@444, one or more ' .
                    'attributes have been modified since that revision.'
            );
        });
    });

    describe('->reference()', function () {
        it('returns the original reference', function () {
            expect($this->subject->reference())->to->equal($this->reference);
        });
    });
});
