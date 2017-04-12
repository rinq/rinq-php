<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Psr\Log\LoggerInterface;
use Rinq\Ident\PeerId;

class Logging
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logStartedListening(PeerId $peerId, string $namespace)
    {
        $this->logger->info(
            sprintf(
                '%s started listening for command requests in \'%s\' namespace',
                $peerId->shortString(),
                $namespace
            )
        );
    }

    public function logStoppedListening(PeerId $peerId, string $namespace)
    {
        $this->logger->info(
            sprintf(
                '%s stopped listening for command requests in \'%s\' namespace',
                $peerId->shortString(),
                $namespace
            )
        );
    }
/*
// loggingResponse wraps are "parent" response and emits a log entry when it is
// closed.
type loggingResponse struct {
    req rinq.Request
    res rinq.Response

    peerID    ident.PeerID
    traceID   string
    logger    rinq.Logger
    startedAt time.Time
}

    public function newLoggingResponse(
    req rinq.Request,
    res rinq.Response,
        PeerId $peerId,
    traceID string,
) rinq.Response {
    return &loggingResponse{
    res: res,
    req: req,

    peerID:    peerID,
    traceID:   traceID,
    logger:    logger,
    startedAt: time.Now(),
    }
}

    public function (r *loggingResponse) IsRequired() bool {
    return r.res.IsRequired()
}

    public function (r *loggingResponse) IsClosed() bool {
    return r.res.IsClosed()
}

    public function (r *loggingResponse) Done(payload *rinq.Payload) {
    r.res.Done(payload)
    r.logSuccess(payload)
}

    public function (r *loggingResponse) Error(err error) {
    r.res.Error(err)

    if failure, ok := err.(rinq.Failure); ok {
    r.logFailure(failure.Type, failure.Payload)
    } else {
    r.logError(err)
    }
}

    public function (r *loggingResponse) Fail(f, t string, v ...interface{}) rinq.Failure {
    err := r.res.Fail(f, t, v...)
    r.logFailure(f, nil)
    return err
}

    public function (r *loggingResponse) Close() bool {
    if r.res.Close() {
    r.logSuccess(nil)
    return true
    }

    return false
}

    public function (r *loggingResponse) logSuccess(payload *rinq.Payload) {
    r.logger.Log(
    "%s handled %s '%s' command from %s successfully (%dms %d/i %d/o) [%s]",
    r.peerID.ShortString(),
    r.req.Namespace,
    r.req.Command,
    r.req.Source.Ref().ShortString(),
    time.Since(r.startedAt)/time.Millisecond,
    r.req.Payload.Len(),
    payload.Len(),
    r.traceID,
    )
}
    public function (r *loggingResponse) logFailure(failureType string, payload *rinq.Payload) {
    r.logger.Log(
    "%s handled %s '%s' command from %s: '%s' failure (%dms %d/i %d/o) [%s]",
    r.peerID.ShortString(),
    r.req.Namespace,
    r.req.Command,
    r.req.Source.Ref().ShortString(),
    failureType,
    time.Since(r.startedAt)/time.Millisecond,
    r.req.Payload.Len(),
    payload.Len(),
    r.traceID,
    )
}

    public function (r *loggingResponse) logError(err error) {
    r.logger.Log(
    "%s handled %s '%s' command from %s: '%s' error (%dms %d/i 0/o) [%s]",
    r.peerID.ShortString(),
    r.req.Namespace,
    r.req.Command,
    r.req.Source.Ref().ShortString(),
    err,
    time.Since(r.startedAt)/time.Millisecond,
    r.req.Payload.Len(),
    r.traceID,
    )*/

    private $logger;
}
