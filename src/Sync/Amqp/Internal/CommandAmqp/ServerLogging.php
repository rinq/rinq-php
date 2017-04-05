<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Psr\Log\LoggerInterface;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;

class ServerLogging
{
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
/*
    public function logServerInvalidMessageID(
        PeerId $peerId,
    msgID string,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
    "%s server ignored AMQP message, '%s' is not a valid message ID",
    $peerId->shortString(),
    msgID,
    )
}

    public function logIgnoredMessage(
        PeerId $peerId,
    msgID ident.MessageID,
    err error,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
    "%s server ignored AMQP message %s, %s",
    $peerId->shortString(),
    msgID.ShortString(),
    err,
    )
}

    public function logRequestBegin(
    ctx context.Context,
        PeerId $peerId,
    msgID ident.MessageID,
    req rinq.Request,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
    "%s server began '%s::%s' command request %s [%s] <<< %s",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    trace.Get(ctx),
    req.Payload,
    )
}

    public function logRequestEnd(
    ctx context.Context,
        PeerId $peerId,
    msgID ident.MessageID,
    req rinq.Request,
    payload *rinq.Payload,
    err error,
) {
        if(!$this->logger) {
            return;
        }

    switch e := err.(type) {
    case nil:
        $this->logger->debug(
            sprintf(
    "%s server completed '%s::%s' command request %s successfully [%s] >>> %s",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    trace.Get(ctx),
    payload,
    )
    case rinq.Failure:
    var message string
    if e.Message != "" {
    message = ": " + e.Message
    }

        $this->logger->debug(
            sprintf(
    "%s server completed '%s::%s' command request %s with '%s' failure%s [%s] <<< %s",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    e.Type,
    message,
    trace.Get(ctx),
    payload,
    )
    default:
        $this->logger->debug(
            sprintf(
    "%s server completed '%s::%s' command request %s with error [%s] <<< %s",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    trace.Get(ctx),
    err,
    )
    }
}

    public function logNoLongerListening(
        PeerId $peerId,
    msgID ident.MessageID,
    ns string,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
    "%s is no longer listening to '%s' namespace, request %s has been re-queued",
    $peerId->shortString(),
    ns,
    msgID.ShortString(),
    )
}

    public function logRequestRequeued(
    ctx context.Context,
        PeerId $peerId,
    msgID ident.MessageID,
    req rinq.Request,
) {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
    "%s did not write a response for '%s::%s' command request, request %s has been re-queued [%s]",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    trace.Get(ctx),
    )
}

    public function logRequestRejected(
    ctx context.Context,
        PeerId $peerId,
    msgID ident.MessageID,
    req rinq.Request,
    reason string,
) {
        $this->logger->debug(
            sprintf(
    "%s did not write a response for '%s::%s' command request %s, request has been abandoned (%s) [%s]",
    $peerId->shortString(),
    req.Namespace,
    req.Command,
    msgID.ShortString(),
    reason,
    trace.Get(ctx),
    )
}
*/
    public function logServerStart( PeerId $peerId, int $preFetch )
    {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s server started with (pre-fetch: %d)',
                $peerId->shortString(),
                $preFetch
            )
        );
    }

    public function logServerStopping( PeerId $peerId, int $pending )
    {
        if(!$this->logger) {
            return;
        }

        $this->logger->debug(
            sprintf(
                '%s server is stopping gracefully (pending: %d)',
                $peerId->shortString(),
                $pending
            )
        );
    }

    public function logServerStop( PeerId $peerId, string $error = null)
    {
        if(!$this->logger) {
            return;
        }

        if (null === $error) {
            $this->logger->debug(
                sprintf(
                    '%s server stopped',
                    $peerId->shortString()
                )
            );
        } else {
            $this->logger->debug(
                sprintf(
                    '%s server stopped: %s',
                    $peerId->shortString(),
                    $error
                )
            );
        }
    }

    private $logger;
}
