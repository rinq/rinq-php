<?php

declare(strict_types=1);    // @codeCoverageIgnore

namespace Rinq;

/**
 * Request holds information about an incoming command request.
 */
final class Request
{
    /**
     * @param Revision $source      The revision of the session that sent the request, at the time it was sent.
     * @param string   $namespace   Namespace is the command namespace.
     * @param string   $command     Command is the application-defined command name for the request.
     * @param mixed    $payload     Payload contains optional application-defined information about the request.
     * @param bool     $IsMulticast True if the command request was (potentially) sent to more than one peer.
     */
    public static function create(
        $source, // TODO: fix this -> Revision $source,
        string $namespace,
        string $command,
        $payload,
        bool $IsMulticast
    ): self {
        return new self($source, $namespace, $command, $payload, $IsMulticast);
    }

    /**
     * Source is the revision of the session that sent the request, at the time
     * it was sent (which is not necessarily the latest).
     */
    public function source(): Revision
    {
        return $this->source;
    }

    /**
     * Namespace is the command namespace. Namespaces are used to route command
     * requests to the appropriate peer and comand handler.
     */
    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * Command is the application-defined command name for the request. The
     * command is logged for each request.
     */
    public function command(): string
    {
        return $this->command;
    }

    /**
     * Payload contains optional application-defined information about the
     * request, such as arguments to the command.
     *
     * @return mixed
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * IsMulticast is true if the command request was (potentially) sent to more
     * than one peer using Session.ExecuteMany().
     */
    public function isMulticast()
    {
        return $this->isMulticast;
    }

    /**
     * @param Revision $source    The revision of the session that sent the request, at the time it was sent.
     * @param string   $namespace Namespace is the command namespace.
     * @param string   $command   Command is the application-defined command name for the request.
     * @param mixed    $payload   Payload contains optional application-defined information about the request.
     * @param bool     $IsMulticast True if the command request was (potentially) sent to more than one peer.
     */
    private function __construct(
        $source, // TODO: fix this -> Revision $source,
        string $namespace,
        string $command,
        $payload,
        bool $IsMulticast
    ) {
        $this->source = $source;
        $this->namespace = $namespace;
        $this->command = $command;
        $this->payload = $payload;
        $this->IsMulticast = $IsMulticast;
    }

    private $source;
    private $namespace;
    private $command;
    private $payload;
    private $IsMulticast;
}
