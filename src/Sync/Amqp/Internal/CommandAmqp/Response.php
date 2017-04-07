<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Bunny\Channel;
use CBOR\CBOREncoder;
use Rinq\Context;
use Rinq\Failure;
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
        string $replyMode
    ) {
        $this->context = $context; // TODO: not really used yet.
        $this->channel = $channel;
        $this->messageId = $messageId;
        $this->request = $request; // TODO: not really used.
        $this->replyMode = $replyMode;

        $this->isClosed = false;
    }

    /**
     * If the response is not required, any payload data sent is discarded.
     * The response must always be closed, even if IsRequired() returns false.
     *
     * @return bool True if the sender is waiting for the response.
     */
    public function isRequired(): bool
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
    public function done($payload): void
    {
        if ($this->isClosed()) {
            // TODO: create exception for this.
            throw new BlahException('Responder is already closed.');
        }

        $headers = [];
        Message::packSuccessResponse($headers);
        $this->respond($headers, $payload);
    }

    /**
     * Send an error to the source session and closes the response.
     *
     * @throws BlahException If the response has already been closed.
     */
    public function error($error): void
    {
        if ($this->isClosed()) {
            // TODO: create exception for this.
            throw new BlahException('Responder is already closed.');
        }

        $headers = [];
        Message::packErrorResponse($headers, $error);
        $this->respond($headers, $error);
    }

    /**
     * A convenience method that creates a Failure and passes it to
     * {@see $this->error()} method. The created failure is returned.
     *
     * The failure $type is used verbatim. The failure message is formatted
     * according to the format specifier $message, interpolated with values from
     * $vars.
     *
     * @throws BlahException If the response has already been closed or if failureType is empty.
     */
    public function fail(string $type, string $message, $vars): Failure
    {
        $error = Failure::create($type, sprintf($message, ...$vars));
        $this->error($error);

        return $error;
    }

    /**
     * Close finalizes the response.
     *
     * If the origin session is expecting response it will receive a null
     * payload.
     *
     * It is not an error to close a responder multiple times. The return value
     * is true the first time Close() is called, and false on subsequent calls.
     */
    public function close(): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        $headers = [];
        Message::packSuccessResponse($headers);
        $this->respond($headers, null);

        return true;
    }

    public function finalize(): bool
    {
        if ($this->isClosed()) {
            return true;
        }

        $this->isClosed = true;

        return false;
    }

    private function respond(array $headers, $payload)
    {
        $this->isClosed = true;
        $headers = [];

        if ($this->replyMode === Message::replyNone) {
            return;
        }

        // TODO
        // if _, err := amqputil.PackDeadline(r.context, msg); err != nil {
        // // the context deadline has already passed
        // return
        // }

        // TODO:
        // channel, err := r.channels.Get()
        // if err != nil {
        // panic(err)
        // }
        // defer r.channels.Put(channel)
        //
        // amqputil.PackTrace(r.context, msg)
        //
        if ($this->replyMode === Message::replyUncorrelated) {
            $this->mesage->packNamespaceAndCommand(
                $headers,
                $this->request->namespace(),
                $this->request->command()
            );
            Message::packReplyMode($headers, $this->replyMode);
        }

        $this->channel->publish(
            CBOREncoder::encode($payload),
            $headers,
            Exchanges::responseExchange,
            $this->messageId
        );
    }

    private $context;
    private $channel;
    private $messageId;
    private $request;
    private $replyMode;
    private $isClosed;
}
