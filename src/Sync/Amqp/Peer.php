<?php

declare(strict_types=1); // @codeCoverageIgnore

namespace Rinq\Sync\Amqp;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Bunny\Exception\ClientException;
use Rinq\Ident\PeerId;
use Rinq\Ident\SessionId;
use Rinq\Internal\Command\Invoker;
use Rinq\Internal\Command\Server;
use Rinq\Peer as PeerInterface;
use Rinq\Session as SessionInterface;
use Rinq\Sync\Amqp\Internal\LocalSession\Session;

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
    public function session(): SessionInterface
    {
        // TODO
        // cat := localsession.NewCatalog(id, p.logger)
        $session = new Session(
            SessionId::create($this->peerId, rand(0,10)), // TODO: Need actual sequence and not rand. see -> Seq:  atomic.AddUint32(&p.seq, 1),
            // cat,
            $this->invoker
            // p.notifier,
            // p.listener,
            // p.logger,
        );

        // TODO
        // p.localStore.Add(sess, cat)
        // go func() {
        //     <-sess.Done()
        //     p.localStore.Remove(sess.ID())
        // }()

        return $session;
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
        $this->server->listen($namespace, $handler);

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
    public function run(float $timeout): void
    {
        $this->isRunning = true;

        do {
            // TODO: move to helper function
            try {
                // TODO: time run() and subtract from $timeout
                $this->broker->run($timeout);
            } catch (ClientException $e) {
                $error = error_get_last();
                if (stripos($error['message'], 'Interrupted system call') === false) {
                    throw $e;
                }

                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
            }
        } while ($this->isRunning);
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
        $this->isRunning = false;

        $this->server->stop();
        $this->invoker->stop();
        $this->broker->stop();
    }

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
