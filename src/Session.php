<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

/**
 * Session is an interface representing a "local" session, that is, a session
 * created by a peer running in this process.
 *
 * Sessions are the "clients" on a Rinq network, able to issue command requests
 * and send notifications to other sessions.
 *
 * Sessions are created by calling Peer.Session(). The peer that creates a
 * session is called the "owning peer".
 *
 * Each session has an in-memory attribute table, which can be used to store
 * application-defined key/value pairs. A session's attribute table can be
 * modified locally, as well as remotely by peers that have received a command
 * request or notification from the session.
 *
 * The attribute table is versioned. Each revision of the attribute table is
 * represented by the Revision interface.
 *
 * An optimistic-locking strategy is employed to protect the attribute table
 * against concurrent writes. In order for a write to succeed, it must be made
 * through a Revision value that represents the current (most recent) revision.
 *
 * Individual attributes in the table can be "frozen", preventing any further
 * changes to that attribute.
 */
interface Session
{
    /**
     * The session's unique identifier.
     *
     * @return string The session's unique identifier.
     */
    public function id(): string;

    /**
     * CurrentRevision returns the current revision of this session.
     *
     * @return Revision Current revision of this session.
     *
     * @throws InvalidRevisionException If the session has been closed, and rev
     * is invalid.
     */
    public function currentRevision(): Revision;

    /**
     * Call sends a command request to the next available peer listening to the
     * $namespace namespace and waits for a response.
     *
     * In the context of the call, the sessions owning peer is the "client" and
     * the listening peer is the "server". The client and server may be the same
     * peer.
     *
     * Both $command and $out are passed to the command handler on the server.
     *
     * Calls always use a deadline; if $context does not have a deadline, a
     * timeout equal to Config::DEFAULT_TIMEOUT is used.
     *
     * If the call completes successfully, err is nil and in is the
     * application-defined response payload sent by the server.
     *
     * IsFailure(err) returns true if the error is an application-defined
     * failure. Failures are server-side errors that are part of the command's
     * public API, as opposed to unexpected errors. If err is a failure, out
     * contains the failure's application-defined payload; for this reason
     * out.Close() must be called, even if err is non-nil.
     *
     * @param string $command Application-defined command name
     * @param object $out     Application-defined request payload.
     *
     * @return mixed The repsonse of the invoked call.
     *
     * @throws ServerException If the error occurred on the server.
     * @throws ClientException For application-defined failures.
     * @throws NotFoundException If the Session has been closed and the command
     * request can not be sent.
     */
    public function call(
        Context $context,
        string $namespace,
        string $command,
        Payload $out
    ): Payload;

    /**
     * CallAync sends a command request to the next available peer listening to
     * the $namespace namespace and instructs it to send a response, but does not block.
     *
     * $command and out are an application-defined command name and request
     * payload, respectively. Both are passed to the command handler on the
     * server.
     *
     * id is a value identifying the outgoing command request.
     *
     * When a response is received, the handler specified by SetAsyncHandler()
     * is invoked. It is passed the id, namespace and command name of the
     * request, along with the response payload and error.
     *
     * It is the application's responsibility to correlate the request with the
     * response and handle the context deadline. The request is NOT tracked by
     * the session and as such the handler is never invoked in the event of a
     * timeout.
     *
     * If IsNotFound(err) returns true, the session has been closed and the
     * command request can not be sent.
     */
    public function callAsync(
        Context $context,
        string $namespace,
        string $command,
        Payload out
    ): MessageID;

    // SetAsyncHandler sets the asynchronous call handler.
    //
    // h is invoked for each command response received to a command request made
    // with CallAsync().
    //
    // If IsNotFound(err) returns true, the session has been closed and the
    // command request can not be sent.
    SetAsyncHandler(h AsyncHandler) error

    // Execute sends a command request to the next available peer listening to
    // the $namespace namespace and returns immediately.
    //
    // $command and out are an application-defined command name and request payload,
    // respectively. Both are passed to the command handler on the server.
    //
    // If IsNotFound(err) returns true, the session has been closed and the
    // command request can not be sent.
    Execute($context context.Context, $namespace, $command string, out *Payload) (err error)

    // Execute sends a command request to all peers listening to the $namespace
    // namespace and returns immediately.
    //
    // Only those peers that are connected to the network at the time the
    // request is sent will receive it. Requests are not queued for future peers.
    //
    // $command and out are an application-defined command name and request payload,
    // respectively. Both are passed to the command handler on the server.
    //
    // If IsNotFound(err) returns true, the session has been closed and the
    // command request can not be sent.
    ExecuteMany($context context.Context, $namespace, $command string, out *Payload) (err error)

    // Notify sends a message directly to another session.
    //
    // t and out are an application-defined notification type and payload,
    // respectively. Both are passed to the notification handler configured on
    // the session identified by s.
    //
    // If IsNotFound(err) returns true, this session has been closed and the
    // notification can not be sent.
    Notify($context context.Context, s SessionID, t string, out *Payload) (err error)

    // NotifyMany sends a message to multiple sessions.
    //
    // The constraint c is a set of attribute key/value pairs that a session
    // must have in it's attribute table in order to receive the notification.
    //
    // t and out are an application-defined notification type and payload,
    // respectively. Both are passed to the notification handlers configured on
    // those sessions that match c.
    //
    // If IsNotFound(err) returns true, this session has been closed and the
    // notification can not be sent.
    NotifyMany($context context.Context, c Constraint, t string, out *Payload) error

    // Listen begins listening for notifications sent to this session.
    //
    // h is invoked on its own goroutine for each notification.
    Listen(h NotificationHandler) error

    // Unlisten stops listening for notifications.
    //
    // If the session is not currently listening for notifications, nil is
    // returned immediately.
    Unlisten() error

    // Close destroys the session after any pending calls have completed.
    Close()

    // Done returns a channel that is closed when the session is closed.
    //
    // The session may be closed directly with Close(), or via a Revision that
    // refers to this session, either locally or remotely.
    //
    // All sessions are closed when their owning peer is stopped.
    Done() <-chan struct{}
}
