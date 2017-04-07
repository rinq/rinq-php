<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Psr\Log\LoggerInterface;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;

class InvokerLogging
{
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
/*
    public function logInvokerInvalidMessageID(
        PeerId $peerId,
        string $messageId
    ) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s invoker ignored AMQP message, \'%s\' is not a valid message ID.',
                $peerID.ShortString(),
                $messageId
            )
        );
    }

    public function logInvokerIgnoredMessage(
        PeerId $peerId,
        MessageId $messageId,
        err error,
    ) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker ignored AMQP message %s, %s',
    peerID.ShortString(),
    msgID.ShortString(),
    err,
    )
}

    public function logUnicastCallBegin(
    PeerId $peerId,
    MessageId $messageId,
    target ident.PeerID,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker began unicast '%s::%s' call %s to %s [%s] >>> %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    target.ShortString(),
    traceID,
    payload,
    )
}

    public function logBalancedCallBegin(
    PeerId $peerId,
    MessageId $messageId,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker began '%s::%s' call %s [%s] >>> %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    traceID,
    payload,
    )
}

    public function logCallEnd(
    PeerId $peerId,
    MessageId $messageId,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
    err error,
) {
        if(!$this->logger) {
            return;
        }

    switch e := err.(type) {
    case nil:
        $this->logger->debug(
    '%s invoker completed '%s::%s' call %s successfully [%s] <<< %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    traceID,
    payload,
    )
    case rinq.Failure:
    var message string
    if e.Message != "" {
    message = ": " + e.Message
    }

        $this->logger->debug(
    '%s invoker completed '%s::%s' call %s with '%s' failure%s [%s] <<< %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    e.Type,
    message,
    traceID,
    payload,
    )
    default:
        $this->logger->debug(
    '%s invoker completed '%s::%s' call %s with error [%s] <<< %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    traceID,
    err,
    )
    }
}

    public function logAsyncRequest(
    PeerId $peerId,
    MessageId $messageId,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
    err error,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker sent asynchronous '%s::%s' call request %s [%s] >>> %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    traceID,
    payload,
    )
}

    public function logAsyncResponse(
    PeerId $peerId,
    MessageId $messageId,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
    err error,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker received asynchronous '%s::%s' call response %s [%s] >>> %s',
    peerID.ShortString(),
    ns,
    cmd,
    msgID.ShortString(),
    traceID,
    payload,
    )
}
*/
    public function logBalancedExecute(
        PeerId $peerId,
        MessageId $messageId,
        string $namespace,
        string $command,
        string $traceId,
        $payload
    ) {
        if (!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s invoker sent \'%s::%s\' execution %s [%s] >>> %s',
                $peerId->shortString(),
                $namespace,
                $command,
                $messageId->shortString(),
                $traceId,
                $payload
            )
        );
    }

    public function logMulticastExecute(
        PeerId $peerId,
        MessageId $messageId,
        string $namespace,
        string $command,
        string $traceId,
        $payload
    ) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s invoker sent multicast \'%s::%s\' execution %s [%s] >>> %s',
                $peerId->shortString(),
                $namespace,
                $command,
                $messageId->shortString(),
                $traceId,
                $payload
            )
        );
    }

    public function logInvokerStart(PeerId $peerId, int $preFetch)
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s invoker started (pre-fetch: %d)',
                $peerId->shortString(),
                $preFetch
            )
        );
    }
/*
    public function logInvokerStopping(
    PeerId $peerId,
    pending int,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
    '%s invoker stopping gracefully (pending: %d)',
    peerID.ShortString(),
    pending,
    )
}

    public function logInvokerStop(
    PeerId $peerId,
    err error,
) {
        if(!$this->logger) {
            return;
        }

    if err == nil {
        $this->logger->debug(
    '%s invoker stopped',
    peerID.ShortString(),
    )
    } else {
        $this->logger->debug(
    '%s invoker stopped: %s',
    peerID.ShortString(),
    err,
    )
    }
*/
    private $logger;
}
