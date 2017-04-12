<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

use Throwable;
use Bunny\Message as BunnyMessage;
use CBOR\CBOREncoder;
use Rinq\Exception\FailureException;

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

    // public static function unpackNamespaceAndCommand(BunnyMessage $message): array
    // {
    //     return [
    //         $this->unpack($message, self::namespaceHeader),
    //         $this->unpack($message, self::commandHeader),
    //     ];
    // }

    public static function packReplyMode(
        array &$headers,
        string $replyMode
    ): void {
        if (in_array($replyMode, $this->replyModes())) {
            $headers['reply-to'] = $replyMode;
        }
    }

    // public static function unpackReplyMode(BunnyMessage $message): string
    // {
    //     return $this->unpack($message, 'reply-to');
    // }

    public static function packRequest(
        array &$headers,
        string $namespace,
        string $command,
        // $payload,
        string $replyMode
    ): void {
        $this->packNamespaceAndCommand($headers, $namespace, $command);
        $this->packReplyMode($headers, $replyMode);
        // $message->context = CBOREncoder::encode($payload);
    }

    public static function packSuccessResponse(&$headers)
    {
        $headers['type'] = self::successResponse;
        // $message->content = CBOREncoder::encode($message->content);
    }

    public static function packErrorResponse(array &$headers, Throwable $error)
    {
        if ($error instanceof FailureException) {
            $headers['type'] = self::failureResponse;
            $headers[self::failureTypeHeader] = $error->type();

            if ($error->getMessage()) {
                $headers[self::failureMessageHeader] = $error->getMessage();
            }
        } else {
            $headers['type'] = self::errorResponse;
        }
    }

    // public static function unpackResponse(array &$headers)
    // {
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
    // }

    private function replyModes()
    {
        return [
            self::replyNone,
            self::replyCorrelated,
            self::replyUncorrelated,
        ];
    }

    // private function unpack(BunnyMessage $message, $key)
    // {
    //     if (array_key_exists($key, $message->headers)) {
    //         return $message->headers[$key];
    //     }
    //
    //     return null;
    // }
}
