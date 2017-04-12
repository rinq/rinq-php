<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq;

use Foo\AsyncHandler;
use Foo\NotificationHandler;
use Rinq\Exception\NotFoundException;
use Rinq\Ident\MessageId;
use Rinq\Ident\SessionId;

/**
 * Session is an interface representing a "local" session, that is, a session
 * created by a peer running in this process.
 *
 * Sessions are the "clients" on a Rinq network, able to issue command requests
 * and send notifications to other sessions.
 *
 * Sessions are created by calling {@see Peer::session()}. The peer that creates a
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
     */
    public function id(): SessionId;

    /**
     * CurrentRevision returns the current revision of this session.
     *
     * @throws NotFoundException If the session has been closed, and revision is invalid.
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
     * Both $command and $payload are passed to the command handler on the server.
     *
     * Calls always use a timeout; if $context does not have a timeout, a
     * timeout equal to $config->defaultTimeout is used.
     *
     * @param Context $context   Context of the call is to be invoked in.
     * @param string  $namespace The namespace of the command request.
     * @param string  $command   Application-defined command name.
     * @param mixed   $payload   Application-defined request payload.
     *
     * @return mixed The response of the invoked call.
     *
     * @throws CommandException          If the error occurred on the server.
     * @throws FailureException          For application-defined failures.
     * @throws NotFoundException         If the Session has been closed and the command request can not be sent.
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function call(
        Context $context,
        string $namespace,
        string $command,
        $payload
    );

    /**
     * CallAync sends a command request to the next available peer listening to
     * the $namespace namespace and instructs it to send a response, but does
     * not block.
     *
     * $command and $payload are an application-defined command name and request
     * payload, respectively. Both are passed to the command handler on the
     * server.
     *
     * When a response is received, the handler specified by
     * $this->setAsyncHandler() is invoked. It is passed the MessageId,
     * namespace and command name of the request, along with the response
     * payload and error.
     *
     * It is the application's responsibility to correlate the request with the
     * response and handle the context deadline. The request is NOT tracked by
     * the session and as such the handler is never invoked in the event of a
     * timeout.
     *
     * @param Context $context   Context of the call is to be invoked in.
     * @param string  $namespace The namespace of the command request.
     * @param string  $command   Application-defined command name.
     * @param mixed   $payload   Application-defined request payload.
     *
     * @return MessageId A value identifying the outgoing command request.
     *
     * @throws CommandException          If the error occurred on the server.
     * @throws FailureException          For application-defined failures.
     * @throws NotFoundException         If the Session has been closed and the command request can not be sent.
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function callAsync(
        Context $context,
        string $namespace,
        string $command,
        $payload
    ): MessageId;

    /**
     * SetAsyncHandler sets the asynchronous call handler.
     *
     * $handler is invoked for each command response received to a command
     * request made with $this->callAsync().
     *
     * @throws NotFoundException If the Session has been closed and the handler can not be set.
     */
    public function setAsyncHandler(AsyncHandler $handler);

    /**
     * Execute sends a command request to the next available peer listening to
     * the $namespace namespace and returns immediately.
     *
     * $command and $payload are an application-defined command name and request
     * payload, respectively. Both are passed to the command handler on the
     * server.
     *
     * @param Context $context   Context of the call is to be invoked in.
     * @param string  $namespace The namespace of the command request.
     * @param string  $command   Application-defined command name.
     * @param mixed   $payload   Application-defined request payload.
     *
     * @throws NotFoundException         If the Session has been closed and the command request can not be sent.
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function execute(
        Context $context,
        string $namespace,
        string $command,
        $payload
    ): void;

    /**
     * Execute sends a command request to all peers listening to the $namespace
     * namespace and returns immediately.
     *
     * Only those peers that are connected to the network at the time the
     * request is sent will receive it. Requests are not queued for future peers.
     *
     * $command and $payload are an application-defined command name and request
     * payload, respectively. Both are passed to the command handler on the
     * server.
     *
     * @param Context $context   Context of the call is to be invoked in.
     * @param string  $namespace The namespace of the command request.
     * @param string  $command   Application-defined command name.
     * @param mixed   $payload   Application-defined request payload.
     *
     * @throws NotFoundException         If the Session has been closed and the command request can not be sent.
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function executeMany(
        Context $context,
        string $namespace,
        string $command,
        $payload
    ): void;

    /**
     * Notify sends a message directly to another session.
     *
     * $type and $payload are an application-defined notification type and
     * payload, respectively. Both are passed to the notification handler
     * configured on the session identified by $target.
     *
     * @param Context   $context Context of the call is to be invoked in.
     * @param SessionId $target  The id of the session to send the notification to.
     * @param string    $type    Application-defined notification type.
     * @param mixed     $payload Application-defined request payload.
     *
     * @throws NotFoundException If the Session has been closed and the command request can not be sent.
     */
    public function notify(
        Context $context,
        SessionId $target,
        string $type,
        $payload
    ): void;

    /**
     * NotifyMany sends a message to multiple sessions.
     *
     * The constraint $constraint is a set of attribute key/value pairs that a
     * session must have in it's attribute table in order to receive the
     * notification.
     *
     * $type and $payload are an application-defined notification type and payload,
     * respectively. Both are passed to the notification handlers configured on
     * those sessions that match $constraint.
     *
     * @param Context          $context    Context of the call is to be invoked in.
     * @param array<Attribute> $constraint List of attributes a session attribute table must contain.
     * @param string           $type       Application-defined notification type.
     * @param mixed            $payload    Application-defined request payload.
     *
     * @throws NotFoundException If the Session has been closed and the command request can not be sent.
     */
    public function notifyMany(
        Context $context,
        array $constraint,
        string $type,
        $payload
    ): void;

    /**
     * Listen begins listening for notifications sent to this session.
     */
    public function listen(NotificationHandler $handler): void;

    /**
     * Unlisten stops listening for notifications.
     */
    public function unlisten(): void;

    /**
     * Close destroys the session after any pending calls have completed.
     */
    public function close(): void;
}
