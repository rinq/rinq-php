<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Psr\Log\LoggerInterface;
use Rinq\Exception\FailureException;
use Rinq\Ident\MessageId;
use Rinq\Ident\PeerId;

class InvokerLogging
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
/*
    public function logInvokerInvalidMessageID(
        PeerId $peerId,
        string $messageId
    ) {
        $this->logger->debug(
            sprintf(
                '%s invoker ignored AMQP message, \'%s\' is not a valid message ID.',
                $$peerId->shortString(),
                $messageId
            )
        );
    }

    public function logInvokerIgnoredMessage(
        PeerId $peerId,
        MessageId $messageId,
        err error,
    ) {
        $this->logger->debug(
    '%s invoker ignored AMQP message %s, %s',
    $peerId->shortString(),
    $messageId->shortString(),
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
        $this->logger->debug(
    '%s invoker began unicast '%s::%s' call %s to %s [%s] >>> %s',
    $peerId->shortString(),
    $namespace,
    $command,
    $messageId->shortString(),
    target.ShortString(),
    $traceId,
    payload,
    )
}


logCallEnd(i.logger, i.peerID, msgID, $namespace, $command, $traceID, in, erd)



*/
    public function logBalancedCallBegin(
        PeerId $peerId,
        MessageId $messageId,
        string $namespace,
        string $command,
        MessageId $traceId,
        $payload
    ) {
        $this->logger->debug(
            sprintf(
                '%s invoker began \'%s::%s\' call %s [%s] >>> %s',
                $peerId->shortString(),
                $namespace,
                $command,
                $messageId->shortString(),
                $traceId,
                json_encode($payload)
            )
        );
    }

    public function logCallEnd(
        PeerId $peerId,
        MessageId $messageId,
        string $namespace,
        string $command,
        MessageId $traceId,
        $payload,
        $error = null
    ) {
        if (null === $error) {
            $this->logger->debug(
                sprintf(
                    '%s invoker completed \'%s::%s\' call %s successfully [%s] <<< %s',
                    $peerId->shortString(),
                    $namespace,
                    $command,
                    $messageId->shortString(),
                    $traceId,
                    json_encode($payload)
                )
            );
        } else if ($error instanceof FailureException) {
            $message = '';
            if (null !== $error->message()) {
                $message = ": " . $error->message();
            }

            $this->logger->debug(
                sprintf(
                    '%s invoker completed \'%s::%s\' call %s with \'%s\' failure%s [%s] <<< %s',
                    $peerId->shortString(),
                    $namespace,
                    $command,
                    $messageId->shortString(),
                    $error->type(),
                    $message,
                    $traceId,
                    json_encode($payload)
                )
            );
        } else {
            $this->logger->debug(
                sprintf(
                    '%s invoker completed \'%s::%s\' call %s with error [%s] <<< %s',
                    $peerId->shortString(),
                    $namespace,
                    $command,
                    $messageId->shortString(),
                    $traceId,
                    $error
                )
            );
        }
    }
/*
    public function logAsyncRequest(
    PeerId $peerId,
    MessageId $messageId,
    string $namespace,
    string $command,
    string $traceId,
    $payload,
    err error,
) {
        $this->logger->debug(
    '%s invoker sent asynchronous '%s::%s' call request %s [%s] >>> %s',
    $peerId->shortString(),
    $namespace,
    $command,
    $messageId->shortString(),
    $traceId,
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
        $this->logger->debug(
    '%s invoker received asynchronous '%s::%s' call response %s [%s] >>> %s',
    $peerId->shortString(),
    $namespace,
    $command,
    $messageId->shortString(),
    $traceId,
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

    public function logInvokerStopping( PeerId $peerId, int $pending)
    {
        $this->logger->debug(
            sprintf(
                '%s invoker stopping gracefully (pending: %d)',
                $peerId->shortString(),
                $pending
            )
        );
    }

    public function logInvokerStop(
        PeerId $peerId,
        $error = null
    ) {
        if (null === $error) {
            $this->logger->debug(
                sprintf(
                    '%s invoker stopped',
                    $peerId->shortString()
                )
            );
        } else {
            $this->logger->debug(
                sprintf(
                    '%s invoker stopped: %s',
                    $peerId->shortString(),
                    $error
                )
            );
        }
    }

    private $logger;
}
