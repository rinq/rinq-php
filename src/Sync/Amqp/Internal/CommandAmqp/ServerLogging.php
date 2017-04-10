<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Psr\Log\LoggerInterface;
use Rinq\Failure;
use Rinq\Context;
use Rinq\Request;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;

class ServerLogging
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logServerInvalidMessageID(PeerId $peerId, string $messageId)
    {
        $this->logger->debug(
            sprintf(
                '%s server ignored AMQP message, \'%s\' is not a valid message ID',
                $peerId->shortString(),
                $messageId
            )
        );
    }

/*
    public function logIgnoredMessage(
        PeerId $peerId,
        MessageId $messageId,
    err error,
) {

        $this->logger->debug(
            sprintf(
    "%s server ignored AMQP message %s, %s",
    $peerId->shortString(),
    $messageId->shortString(),
    err,
    )
}
*/
    public function logRequestBegin(
        Context $context,
        PeerId $peerId,
        MessageId $messageId,
        Request $request
    ) {

        $this->logger->debug(
            sprintf(
                '%s server began \'%s::%s\' command request %s [%s] <<< %s',
                $peerId->shortString(),
                $request->namespace(),
                $request->command(),
                $messageId->shortString(),
                $context->traceId(),
                json_encode($request->payload())
            )
        );
    }

    public function logRequestEnd(
        Context $context,
        PeerId $peerId,
        MessageId $messageId,
        Request $request,
        $payload,
        string $error = null
    ) {

        if (null === $error) {
            $this->logger->debug(
                sprintf(
                    "%s server completed '%s::%s' command request %s successfully [%s] >>> %s",
                    $peerId->shortString(),
                    $request->namespace(),
                    $request->command(),
                    $messageId->shortString(),
                    $context->traceId(),
                    json_encode($payload)
                )
            );
        } else if ($error instanceof Failure) {
            $message = '';
            if (null !== $error->message()) {
                $message = ": " . $error->message();
            }

            $this->logger->debug(
                sprintf(
                    '%s server completed \'%s::%s\' command request %s with \'%s\' failure%s [%s] <<< %s',
                    $peerId->shortString(),
                    $request->namespace(),
                    $request->command(),
                    $messageId->shortString(),
                    $error->type(),
                    $messsage,
                    $context->traceId(),
                    json_encode($payload)
                )
            );
        } else {
            $this->logger->debug(
                sprintf(
                    '%s server completed \'%s::%s\' command request %s with error [%s] <<< %s',
                    $peerId->shortString(),
                    $request->namespace(),
                    $request->command(),
                    $messageId->shortString(),
                    $context->traceId(),
                    $error
                )
            );
        }
    }
/*
    public function logNoLongerListening(
        PeerId $peerId,
        MessageId $messageId,
    ns string,
) {

        $this->logger->debug(
            sprintf(
    "%s is no longer listening to '%s' namespace, request %s has been re-queued",
    $peerId->shortString(),
    ns,
    $messageId->shortString(),
    )
}

    public function logRequestRequeued(
        Context $context,
        PeerId $peerId,
        MessageId $messageId,
        Request $request,
) {

        $this->logger->debug(
            sprintf(
    "%s did not write a response for '%s::%s' command request, request %s has been re-queued [%s]",
    $peerId->shortString(),
    $request->namespace(),
    $request->command(),
    $messageId->shortString(),
    $context->traceId()
    )
}

    public function logRequestRejected(
        Context $context,
        PeerId $peerId,
        MessageId $messageId,
        Request $request,
    reason string,
) {
        $this->logger->info(
            sprintf(
    "%s did not write a response for '%s::%s' command request %s, request has been abandoned (%s) [%s]",
    $peerId->shortString(),
    $request->namespace(),
    $request->command(),
    $messageId->shortString(),
    reason,
    $context->traceId()
    )
}
*/
    public function logServerStart(PeerId $peerId, int $preFetch)
    {
        $this->logger->debug(
            sprintf(
                '%s server started with (pre-fetch: %d)',
                $peerId->shortString(),
                $preFetch
            )
        );
    }

    public function logServerStopping(PeerId $peerId, int $pending)
    {
        $this->logger->debug(
            sprintf(
                '%s server is stopping gracefully (pending: %d)',
                $peerId->shortString(),
                $pending
            )
        );
    }

    public function logServerStop(PeerId $peerId, string $error = null)
    {
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
