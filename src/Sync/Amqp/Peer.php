<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Rinq\Ident\PeerId;
use Rinq\Internal\Command\Invoker;
use Rinq\Internal\Command\Server;
use Rinq\Peer as PeerInterface;
use Rinq\Session;

/*
 * Peer represents a connection to a Rinq network.
 *
 * Peers can act as a server, responding to application-defined commands.
 * Use {@see Peer::listen()} to start accepting incoming command requests.
 *
 * Command requests are sent by sessions, represented by the Session interface.
 * Sessions can also send notifications to other sessions. Sessions are created
 * by calling {@see Peer::session()}.
 *
 * Each peer is assigned a unique ID, which is represented by {@see PeerId}.
 * All IDs generated by the peer, such as session IDs and message IDs contain
 * the peer ID, so that they can be traced to their origin easily.
 */
final class Peer implements PeerInterface
{
    public static function create(
        PeerId $peerId,
        Client $broker,
        //     localStore,
        //     remoteStore,
        Invoker $invoker,
        Server $server,
        //     notifier,
        //     listener,
        Logging $logger
    ): self {
        return new self($peerId, $broker, $invoker, $server, $logger);
    }
    /**
     * The peer's unique identifier.
     */
    public function id(): PeerId
    {
        return $this->peerId;
    }

    /**
     * Session creates and returns a new session owned by this peer.
     *
     * Creating a session does not perform any network IO. The only limit to the
     * number of sessions is the memory required to store them.
     *
     * Sessions created after the peer has been stopped are unusable. Any
     * operation will fail immediately.
     */
    public function session(): Session
    {
        // id := ident.SessionID{
        //     Peer: p.id,
        //     Seq:  atomic.AddUint32(&p.seq, 1),
        // }
        //
        // cat := localsession.NewCatalog(id, p.logger)
        // sess := localsession.NewSession(
        //     id,
        //     cat,
        //     p.invoker,
        //     p.notifier,
        //     p.listener,
        //     p.logger,
        // )
        //
        // p.localStore.Add(sess, cat)
        // go func() {
        //     <-sess.Done()
        //     p.localStore.Remove(sess.ID())
        // }()
        //
        // return sess
    }

    /**
     * Listen starts listening for command requests in the given namespace.
     *
     * When a command request is received with a namespace equal to $namespace,
     * the handler $handler is invoked.
     *
     * Repeated calls to listen() with the same namespace simply changes the
     * handler associated with that namespace.
     *
     * @param string   $namespace The namespace of the command request.
     * @param callable $handler   The handler to fulfil the request.
     *
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function listen(string $namespace, callable $handler): void
    {
        $this->server->listen(
            $namespace,
            function (Message $message, Channel $channel, Client $bunny) use (&$handler) {
                $handler($message, $channel, $bunny);
            }
        );

        $this->logger->logStartedListening($this->peerId, $namespace);
    }

    /**
     * Unlisten stops listening for command requests in the given namepsace.
     *
     * @param string $namespace The namespace to stop listening for.
     *
     * @throws InvalidNamespaceException When namespace is invalid.
     */
    public function unlisten(string $namespace): void
    {
        $this->server->unlisten($namespace);

        $this->logger->logStoppedListening($this->peerId, $namespace);
    }

    /**
     * Handle command requests and notifications until {@see Peer::stop()} is
     * called.
     */
    public function wait(): void
    {
        // select {
        // case <-p.remoteStore.Done():
        // return nil, p.remoteStore.Err()
        //
        // case <-p.invoker.Done():
        // return nil, p.invoker.Err()
        //
        // case <-p.server.Done():
        // return nil, p.server.Err()
        //
        // case <-p.listener.Done():
        // return nil, p.listener.Err()
        //
        // case <-p.sm.Graceful:
        // return p.graceful, nil
        //
        // case <-p.sm.Forceful:
        // return nil, nil
        //
        // case err := <-p.amqpClosed:
        // return nil, err
        // }

        $this->broker->run();
    }

    /**
     * Stop instructs the peer to disconnect from the network in a graceful
     * manner once all pending operations have completed.
     *
     * Any calls to {@see Session::call()}, command handlers or notification handlers
     * must return before the peer has stopped.
     */
    public function stop(): void
    {
        // p.server.GracefulStop()
        // p.invoker.GracefulStop()
        // p.remoteStore.GracefulStop()
        // p.listener.GracefulStop()
        //
        // done := syncutil.Group(
        // p.remoteStore.Done(),
        // p.invoker.Done(),
        // p.server.Done(),
        // p.listener.Done(),
        // )
        //
        // select {
        // case <-done:
        // return nil, nil
        //
        // case <-p.sm.Forceful:
        // return nil, nil
        //
        // case err := <-p.amqpClosed:
        // return nil, err
        // }
    }

    // func (p *peer) finalize(err error) error {
    //     p.server.Stop()
    //     p.invoker.Stop()
    //     p.remoteStore.Stop()
    //     p.listener.Stop()
    //
    //     p.localStore.Each(func(sess rinq.Session, _ localsession.Catalog) {
    //     sess.Destroy()
    //     })
    //
    //     <-syncutil.Group(
    //     p.remoteStore.Done(),
    //     p.invoker.Done(),
    //     p.server.Done(),
    //     p.listener.Done(),
    //     )
    //
    //     closeErr := p.broker.Close()
    //
    //     // only return the close err if there's no causal error.
    //     if err == nil {
    //     return closeErr
    //     }
    //
    //     return err
    // }

    public function __construct(
        PeerId $peerId,
        Client $broker,
        //     localStore,
        //     remoteStore,
        Invoker $invoker,
        Server $server,
        //     notifier,
        //     listener,
        Logging $logger
    ) {
        $this->peerId = $peerId;
        $this->broker = $broker;
        $this->invoker = $invoker;
        $this->server = $server;
        $this->logger = $logger;
    }

    private $peerId;
    private $broker;
    private $invoker;
    private $server;
    private $logger;
}
