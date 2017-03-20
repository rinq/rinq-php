<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\PeerId;
use Rinq\Ident\Reference;
use Rinq\Ident\SessionId;

describe(FrozenAttributeException::class, function () {
    beforeEach(function () {
        $this->reference = Reference::create(SessionId::create(PeerId::create(111, 222), 333), 444);
        $this->subject = new FrozenAttributeException($this->reference);
    });

    describe('->construct()', function () {
        it('should construct a meaningful exception message', function () {
            expect($this->subject->getMessage())->to->equal(
                'Can not update 6F-00DE.333@444, the change-set references one or more frozen keys.'
            );
        });
    });

    describe('->reference()', function () {
        it('should return the original reference', function () {
            expect($this->subject->reference())->to->equal($this->reference);
        });
    });
});
