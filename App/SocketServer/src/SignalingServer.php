<?php

namespace App\SocketServer\src;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Exception;

// TODO: INCLUDE DOCS FOR SIGNALING SERVER ROUTES

class SignalingServer implements MessageComponentInterface
{

    private SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn): void
    {
        // TODO: Implement onOpen() method.

        // get the http request params
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        var_dump($args);
        $conn->name = $args["user"];
        $this->clients->attach($conn);
        $conn->send(json_encode(["status" => "success", "message" => "connection established"]));
    }

    function onClose(ConnectionInterface $conn): void
    {
        // TODO: Implement onClose() method.

        // get the http request params
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        $this->clients->detach($conn);
        $conn->send(json_encode(["status" => "success", "message" => "connection terminated"]));
    }

    function onError(ConnectionInterface $conn, Exception $e): void
    {
        // TODO: Implement onError() method.

        // get the http request params
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        $this->clients->detach($conn);
        $conn->send(json_encode(["status" => "error", "message" => "an error occurred"]));
    }

    function onMessage(ConnectionInterface $from, $msg): void
    {
        // TODO: Implement onMessage() method.

        // get the http request params
        $args = [];
        parse_str($from->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);
        $message = json_decode($msg);
        var_dump($message);
        foreach ($this->clients as $client) {
            if ($from !== $client && $message->target === $client->name) {
                $client->send($msg);
            }
        }
    }
}
