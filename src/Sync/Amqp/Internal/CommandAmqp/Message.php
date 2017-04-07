<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\CommandAmqp;

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

    public function packNamespaceAndCommand(
        array &$headers,
        string $namespace,
        string $command
    ): void {
        $headers[self::namespaceHeader] = $namespace;
        $headers[self::commandHeader] = $command;
    }

    public function unpackNamespaceAndCommand(array $headers): array
    {
        return [
            $this->unpack($headers, self::namespaceHeader),
            $this->unpack($headers, self::commandHeader),
        ];
    }

    // public function packReplyMode(array &$headers, string $replyMode)
    // {
    //     if (
    //         in_array(
    //             $replyMode,
    //             [
    //                 self::replyNone,
    //                 self::replyCorrelated,
    //                 self::replyUncorrelated,
    //             ]
    //         )
    //     ) {
    //         // TODO: Shouldn't this be called 'reply-mode'? so as not to conflict with the correlation id.
    //         $headers['reply-to'] = $replyMode;
    //     }
    // }

    // public function unpackReplyMode(array &$headers): string
    // {
    //     // TODO: Shouldn't this be called 'reply-mode'? so as not to conflict with the correlation id.
    //     return $this->unpack($headers, 'reply-to');
    // }

    // public function packRequest(
    //     array &$headers,
    //     string $namespace,
    //     string $command,
    //     string $replyMode
    // ) {
        // $this->packNamespaceAndCommand($headers, $namespace, $command);
        // $this->packReplyMode($headers, $replyMode);
        // // TODO: need to pack body!
        // msg.Body = p.Bytes()
    // }

    // public function packSuccessResponse(array &$headers, $payload)
    // {
        // TODO
        // msg.Type = successResponse
        // msg.Body = p.Bytes()
    // }

    // public function packErrorResponse(array &$headers, $error)
    // {
        // TODO
        // if f, ok := err.(rinq.Failure); ok {
        // if f.Type == "" {
        //     panic("failure type is empty")
        // }
        //
        // msg.Type = failureResponse
        // msg.Body = f.Payload.Bytes()
        //
        // if $headers == nil {
        // $headers = amqp.Table{}
        // }
        //
        // $headers[failureTypeHeader] = f.Type
        // if f.Message != "" {
        // $headers[failureMessageHeader] = f.Message
        // }
        //
        // } else {
        // msg.Type = errorResponse
        // msg.Body = []byte(err.Error())
        // }
    // }

    // public function unpackResponse(array &$headers)
    // {
        // TODO
        // switch msg.Type {
        // case successResponse:
        // return rinq.NewPayloadFromBytes(msg.Body), nil
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

    // private function unpack($headers, $key)
    // {
    //     if (array_key_exists($key, $headers)) {
    //         return $headers[$key];
    //     }
    //
    //     return null;
    // }
}
