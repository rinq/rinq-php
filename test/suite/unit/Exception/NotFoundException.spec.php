<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Exception;

use Rinq\Ident\SessionId;
use Rinq\Ident\PeerId;

describe('NotFoundException', function () {
    beforeEach(function () {
        $this->sessionId = SessionId::create(PeerId::create(111, 222), 333);
        $this->subject = new NotFoundException($this->sessionId);
    });

    describe('->construct()', function() {
        xit('should construct a meaningful exception message', function() {
            expect($this->subject->getMessage())->to->equal('Session 6F-00DE.333 not found.');
        });
    });

    describe('->id()', function () {
        it('should return the original session id', function () {
            expect($this->subject->id())->to->equal($this->sessionId);
        });
    });
});
