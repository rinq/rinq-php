<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;
use Rinq\Failure;

class Message
{
    /**
     * successResponse is the AMQP message type used for successful call
     * responses.
     */
    public const successResponse = 's';

    /**
     * failureResponse is the AMQP message type used for call responses
     * indicating failure for an "expected" application-defined reason.
     */
    public const failureResponse = 'f';

    /**
     * errorResponse is the AMQP message type used for call responses indicating
     * unepected error or internal error.
     */
    public const errorResponse = 'e';

    /**
     * namespaceHeader specifies the namespace in command requests and
     * uncorrelated command responses.
     */
    public const namespaceHeader = 'n';

    /**
     * commandHeader specifies the command name requests and
     * uncorrelated command responses.
     */
    public const commandHeader = 'c';

    /**
     * failureTypeHeader specifies the failure type in command responses with
     * the "failureResponse" type.
     */
    public const failureTypeHeader = 't';

    /**
     * failureMessageHeader holds the error message in command responses with
     * the "failureResponse" type.
     */
    public const failureMessageHeader = 'm';

    /**
     * replyNone is the AMQP reply-to value used for command requests that are
     * not expecting a reply.
     */
    public const replyNone = '';

    /**
     * replyNormal is the AMQP reply-to value used for command requests that are
     * waiting for a reply.
     */
    public const replyCorrelated = 'c';

    /**
     * replyUncorrelated is the AMQP reply-to value used for command requests
     * that are waiting for a reply, but where the invoker does not have
     * any information about the request. This instruct the server to include
     * request information in the response.
     */
    public const replyUncorrelated = 'u';

    public static function packNamespaceAndCommand(
        array &$headers,
        string $namespace,
        string $command
    ): void {
        $headers[self::namespaceHeader] = $namespace;
        $headers[self::commandHeader] = $command;
    }

    public static function unpackNamespaceAndCommand(array $headers): array
    {
        return [
            $this->unpack($headers, self::namespaceHeader),
            $this->unpack($headers, self::commandHeader),
        ];
    }

    public static function packReplyMode(array &$headers, string $replyMode): void
    {
        if (
            in_array(
                $replyMode,
                [
                    self::replyNone,
                    self::replyCorrelated,
                    self::replyUncorrelated,
                ]
            )
        ) {
            $headers['reply-to'] = $replyMode;
        }
    }

    public static function unpackReplyMode(array &$headers): string
    {
        return $this->unpack($headers, 'reply-to');
    }

    public static function packRequest(
        array &$headers,
        string $namespace,
        string $command,
        string $replyMode
    ): void {
        $this->packNamespaceAndCommand($headers, $namespace, $command);
        $this->packReplyMode($headers, $replyMode);
    }

    public static function packSuccessResponse(array &$headers)
    {
        $headers['type'] = self::successResponse;
    }

    public static function packErrorResponse(array &$headers, $error)
    {
        if ($error instanceof Failure) {
            $headers['type'] = self::failureResponse;
            $headers[self::failureTypeHeader] = $error->type();

            if ($error->message()) {
                $headers[self::failureMessageHeader] = $error->message();
            }
        } else {
            $headers['type'] = self::errorResponse;
        }
    }

    public static function unpackResponse(array &$headers)
    {
        // // TODO
        // switch ($this->unpack['type']) {
        //     case self::successResponse:
        //         return rinq.NewPayloadFromBytes(msg.Body), nil
        //
        // case failureResponse:
        // failureType, _ := $headers[failureTypeHeader].(string)
        // if failureType == "" {
        // return nil, errors.New("malformed response, failure type must be a non-empty string")
        // }
        //
        // failureMessage, _ := $headers[failureMessageHeader].(string)
        //
        // payload := rinq.NewPayloadFromBytes(msg.Body)
        // return payload, rinq.Failure{
        // Type:    failureType,
        // Message: failureMessage,
        // Payload: payload,
        // }
        //
        // case errorResponse:
        // return nil, rinq.CommandError(msg.Body)
        //
        // default:
        // return nil, fmt.Errorf("malformed response, message type '%s' is unexpected", msg.Type)
        // }
    }

    private function unpack($headers, $key)
    {
        if (array_key_exists($key, $headers)) {
            return $headers[$key];
        }

        return null;
    }
}
