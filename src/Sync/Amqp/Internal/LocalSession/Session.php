<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp\Internal\LocalSession;

use Rinq\Context;
use Rinq\Ident\MessageId;
use Rinq\Ident\SessionId;
use Rinq\Internal\Command\Invoker;
use Rinq\Revision;
use Rinq\Session as SessionInterface;
use Foo\AsyncHandler;
use Foo\NotificationHandler;

/**
 * Local Session Store.
 */
class Session implements SessionInterface
{
/*
type session struct {
    id       ident.SessionID
    catalog  Catalog
    invoker  command.Invoker
    notifier notify.Notifier
    listener notify.Listener
    logger   rinq.Logger
    done     chan struct{}

    // mutex guards Call(), Listen(), Unlisten() and Close() so that Close()
    // waits for pending calls to complete or timeout, and to ensure that it's
    // call to listener.Unlisten() is not "undone" by the user.
    mutex sync.RWMutex
}*/

    public function __construct(
        SessionId $sessionId,
        // Catalog $catalog, TODO:
        Invoker $invoker
        // Notifier $notifier, TODO:
        // Listener $listener, TODO:
        // SessionLogging $logger TODO:
    ) {
        $this->sessionId = $sessionId;
        $this->invoker = $invoker;

        // TODO
        // sess.logger.Log(
        // "%s session created",
        // sess.catalog.Ref().ShortString(),
        // )

        // go func() {
        // <-catalog.Done()
        // sess.destroy()
        // }()

    }

    /**
     * The session's unique identifier.
     */
    public function id(): SessionId
    {
        return $this->sessionId;
    }

    /**
     * CurrentRevision returns the current revision of this session.
     *
     * @throws NotFoundException If the session has been closed, and revision is invalid.
     */
    public function currentRevision(): Revision
    {
        // TODO
        // select {
        // case <-s.done:
        // return nil, rinq.NotFoundError{ID: s.id}
        // default:
        // return s.catalog.Head(), nil
        // }
    }

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
    ) {
        // TODO
        // if err := rinq.ValidateNamespace(ns); err != nil {
        // return nil, err
        // }
        //
        // s.mutex.RLock()
        // defer s.mutex.RUnlock()
        //
        // select {
        // case <-s.done:
        // return nil, rinq.NotFoundError{ID: s.id}
        // default:
        // }

        // TODO: need to create actual message id.
        // msgID := s.catalog.NextMessageID()
        $messageId = MessageId::createFromString('58EAFBB2-1C02.1@0#1');

        // start := time.Now()
        $traceId = null; // passed by reference.
        $result = $this->invoker->callBalanced(
            $context,
            $messageId,
            $namespace,
            $command,
            $payload,
            $traceId
        );
        // elapsed := time.Since(start) / time.Millisecond
        //
        // logCall(s.logger, msgID, $namespace, cmd, elapsed, out, in, err, traceID)
        //
        return $result;
    }

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
    ): MessageId {
    // var msgID ident.MessageID
    //
    // if err := rinq.ValidateNamespace(ns); err != nil {
    // return msgID, err
    // }
    //
    // s.mutex.RLock()
    // defer s.mutex.RUnlock()
    //
    // select {
    // case <-s.done:
    // return msgID, rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // msgID = s.catalog.NextMessageID()
    //
    // traceID, err := s.invoker.CallBalancedAsync(ctx, msgID, $namespace, cmd, out)
    // if err != nil {
    // return msgID, err
    // }
    //
    // logAsyncRequest(s.logger, msgID, $namespace, cmd, out, traceID)
    //
    // return msgID, nil
}

    /**
     * SetAsyncHandler sets the asynchronous call handler.
     *
     * $handler is invoked for each command response received to a command
     * request made with $this->callAsync().
     *
     * @throws NotFoundException If the Session has been closed and the handler can not be set.
     */
    public function setAsyncHandler(AsyncHandler $handler)
    {
    // s.mutex.RLock()
    // defer s.mutex.RUnlock()
    //
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // s.invoker.SetAsyncHandler(
    // s.id,
    // func(
    // Context $context,
    // sess rinq.Session,
    // msgID ident.MessageID,
    // ns string,
    // string $command,
    // in *rinq.Payload,
    // err error,
    // ) {
    // logAsyncResponse(ctx, s.logger, msgID, $namespace, cmd, in, err)
    // h(ctx, sess, msgID, $namespace, cmd, in, err)
    // },
    // )
    //
    // return nil
}

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
    ): void {
    // if err := rinq.ValidateNamespace(ns); err != nil {
    // return err
    // }
    //
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // msgID := s.catalog.NextMessageID()
    // traceID, err := s.invoker.ExecuteBalanced(ctx, msgID, $namespace, cmd, p)
    //
    // if err == nil {
    // s.logger.Log(
    // "%s executed '%s::%s' command (%d/o) [%s]",
    // msgID.ShortString(),
    // $namespace,
    // cmd,
    // p.Len(),
    // traceID,
    // )
    // }
    //
    // return err
}

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
    ): void {
    // if err := rinq.ValidateNamespace(ns); err != nil {
    // return err
    // }
    //
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // msgID := s.catalog.NextMessageID()
    // traceID, err := s.invoker.ExecuteMulticast(ctx, msgID, $namespace, cmd, p)
    //
    // if err == nil {
    // s.logger.Log(
    // "%s executed '%s::%s' command on multiple peers (%d/o) [%s]",
    // msgID.ShortString(),
    // $namespace,
    // cmd,
    // p.Len(),
    // traceID,
    // )
    // }
    //
    // return err
}

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
    ): void {
    // if err := target.Validate(); err != nil || target.Seq == 0 {
    // return fmt.Errorf("session ID %s is invalid", target)
    // }
    //
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // msgID := s.catalog.NextMessageID()
    // traceID, err := s.notifier.NotifyUnicast(ctx, msgID, target, typ, p)
    //
    // if err == nil {
    // s.logger.Log(
    // "%s sent '%s' notification to %s (%d/o) [%s]",
    // msgID.ShortString(),
    // typ,
    // target.ShortString(),
    // p.Len(),
    // traceID,
    // )
    // }
    //
    // return err
}

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
    ): void {
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // msgID := s.catalog.NextMessageID()
    // traceID, err := s.notifier.NotifyMulticast(ctx, msgID, con, typ, p)
    //
    // if err == nil {
    // s.logger.Log(
    // "%s sent '%s' notification to sessions matching {%s} (%d/o) [%s]",
    // msgID.ShortString(),
    // typ,
    // con,
    // p.Len(),
    // traceID,
    // )
    // }
    //
    // return err
}

    /**
     * Listen begins listening for notifications sent to this session.
     */
    public function listen(NotificationHandler $handler): void
    {
    // if handler == nil {
    // panic("handler must not be nil")
    // }
    //
    // s.mutex.RLock()
    // defer s.mutex.RUnlock()
    //
    // select {
    // case <-s.done:
    // return rinq.NotFoundError{ID: s.id}
    // default:
    // }
    //
    // changed, err := s.listener.Listen(
    // s.id,
    // func(
    // Context $context,
    // target rinq.Session,
    // n rinq.Notification,
    // ) {
    // rev := s.catalog.Head()
    //
    // s.logger.Log(
    // "%s received '%s' notification from %s (%d/i) [%s]",
    // rev.Ref().ShortString(),
    // n.Type,
    // n.Source.Ref().ShortString(),
    // n.Payload.Len(),
    // trace.Get(ctx),
    // )
    //
    // handler(ctx, target, n)
    // },
    // )
    //
    // if err != nil {
    // return err
    // } else if changed && s.logger.IsDebug() {
    // s.logger.Log(
    // "%s started listening for notifications",
    // s.catalog.Ref().ShortString(),
    // )
    // }
    //
    // return nil
}

    /**
     * Unlisten stops listening for notifications.
     */
    public function unlisten(): void
    {
//     s.mutex.RLock()
//     defer s.mutex.RUnlock()
//
//     select {
//     case <-s.done:
//     return rinq.NotFoundError{ID: s.id}
//     default:
//     }
//
//     changed, err := s.listener.Unlisten(s.id)
//
//     if err != nil {
//     return err
//     } else if changed && s.logger.IsDebug() {
//     s.logger.Log(
//     "%s stopped listening for notifications",
//     s.catalog.Ref().ShortString(),
//     )
//     }
//
//     return nil
// }
//
//     public function destroy() {
//     if s.destroy() {
//     logSessionDestroy(s.logger, s.catalog, "")
//     }
}

    /**
     * Close destroys the session after any pending calls have completed.
     */
    public function close(): void
    {
//     s.mutex.Lock()
//     defer s.mutex.Unlock()
//
//     select {
//     case <-s.done:
//     return false
//     default:
//     close(s.done)
//     s.catalog.Close()
//     s.invoker.SetAsyncHandler(s.id, nil)
//     _, _ = s.listener.Unlisten(s.id)
//     return true
//     }
// }
//
//     public function done() <-chan struct{} {
//     return s.done
}

    private $sessionId;
    private $invoker;
}
