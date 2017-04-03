<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

class Message
{
    /**
     * successResponse is the AMQP message type used for successful call
     * responses.
     */
    public const successResponse = "s";

    /**
     * failureResponse is the AMQP message type used for call responses
     * indicating failure for an "expected" application-defined reason.
     */
    public const failureResponse = "f";

    /**
     * errorResponse is the AMQP message type used for call responses indicating
     * unepected error or internal error.
     */
    public const errorResponse = "e";

    /**
     * namespaceHeader specifies the namespace in command requests and
     * uncorrelated command responses.
     */
    public const namespaceHeader = "n";

    /**
     * commandHeader specifies the command name requests and
     * uncorrelated command responses.
     */
    public const commandHeader = "c";

    /**
     * failureTypeHeader specifies the failure type in command responses with
     * the "failureResponse" type.
     */
    public const failureTypeHeader = "t";

    /**
     * failureMessageHeader holds the error message in command responses with
     * the "failureResponse" type.
     */
    public const failureMessageHeader = "m";

    /**
     * replyNone is the AMQP reply-to value used for command requests that are
     * not expecting a reply.
     */
    public const replyNone = "";

    /**
     * replyNormal is the AMQP reply-to value used for command requests that are
     * waiting for a reply.
     */
    public const replyCorrelated = "c";

    /**
     * replyUncorrelated is the AMQP reply-to value used for command requests
     * that are waiting for a reply, but where the invoker does not have
     * any information about the request. This instruct the server to include
     * request information in the response.
     */
    public const replyUncorrelated = "u";
}
