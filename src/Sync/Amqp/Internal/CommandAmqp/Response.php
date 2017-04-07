<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use Rinq\Context;
use Rinq\Ident\MessageId;
use Rinq\Request;
use Rinq\Response as ResponseInterface;

/**
 * Response sends a reply to incoming command requests.
 */
class Response implements ResponseInterface
{
    public function __construct(
        Context $context,
        Channel $channel,
        MessageId $messageId,
        Request $request,
        string $replyMode,
        Message $message
    ) {
        $this->context = $context;
        $this->channel = $channel;
        $this->messageId = $messageId;
        $this->request = $request;
        $this->replyMode = $replyMode;
        $this->message = $message;

        $this->isClosed = false;
    }

    public function message(Message $message)
    {

    }

    /**
     * If the response is not required, any payload data sent is discarded.
     * The response must always be closed, even if IsRequired() returns false.
     *
     * @return bool True if the sender is waiting for the response.
     */
    public function isRequired() bool
    {
        if ($this->isClosed()) {
            return false;
        }

        if ($this->replyMode == Message::replyNone) {
            return false;
        }

        return true;
    }

    /**
     * IsClosed true If the response has already been closed.
     */
    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    /**
     * Send a payload to the source session and closes the response.
     *
     * @param mixed $payload The body of the response.
     *
     * @throws BlahException If the response has already been closed.
     */
    public function done($payload)
    {
        if ($this->isClosed()) {
            // TODO: create exception for this.
            throw new BlahException('Responder is already closed.');
        }

        // TODO:
        // msg := &amqp.Publishing{}
        Message::packSuccessResponse(BunnyMessage, payload)
        $this->respond(msg)
    }

    /**
     * Send an error to the source session and closes the response.
     *
     * @throws BlahException If the response has already been closed.
     */
    public function error(err error)
    {
        if ($this->isClosed()) {
            // TODO: create exception for this.
            throw new BlahException('Responder is already closed.');
        }

        // TODO:
        // msg := &amqp.Publishing{}
        // packErrorResponse(msg, err)
        // r.respond(msg)
    }

    /**
     * A convenience method that creates a Failure and passes it to
     * {@see $this->error()} method. The created failure is returned.
     *
     * The failure type t is used verbatim. The failure message is formatted
     * according to the format specifier f, interpolated with values from v.
     *
     * @throws BlahException If the response has already been closed or if failureType is empty.
     */
    public function fail(t, f string, v ...interface{}) rinq.Failure
    {
        // TODO
        // err := rinq.Failure{
        // Type:    t,
        // Message: fmt.Sprintf(f, v...),
        // }
        //
        // r.Error(err)
        //
        // return err
    }

    /**
     * Close finalizes the response.
     *
     * If the origin session is expecting response it will receive a nil payload.
     *
     * It is not an error to close a responder multiple times. The return value
     * is true the first time Close() is called, and false on subsequent calls.
     */
    public function close(): bool
    {
        // r.mutex.Lock()
        // defer r.mutex.Unlock()
        //
        // if r.isClosed {
        // return false
        // }
        //
        // msg := &amqp.Publishing{}
        // packSuccessResponse(msg, nil)
        // r.respond(msg)
        //
        // return true
    }

    public function finalize(): bool
    {
        // r.mutex.Lock()
        // defer r.mutex.Unlock()
        //
        // if r.isClosed {
        // return true
        // }
        //
        // r.isClosed = true
        //
        // return false
    }

    private function respond(MessageId $messageId, $payload)
    {
        $this->isClosed = true;

        if ($this->replyMode == Message::replyNone) {
            return;
        }

        // if _, err := amqputil.PackDeadline(r.context, msg); err != nil {
        // // the context deadline has already passed
        // return
        // }

        channel, err := r.channels.Get()
        if err != nil {
        panic(err)
        }
        defer r.channels.Put(channel)

        amqputil.PackTrace(r.context, msg)

        if (r.replyMode == Message::replyUncorrelated) {
        packNamespaceAndCommand(msg, r.request.Namespace, r.request.Command)
        packReplyMode(msg, r.replyMode)
        }

        $this->channel->publish(
            CCBOREncoder::encode($payload),
            [],
            Exchanges::responseExchange,
            $messageId
        );
    }

    private $context;
    private $channel;
    private $messageId;
    private $request;
    private $replyMode;
    private $message;
    private $isClosed;
}
