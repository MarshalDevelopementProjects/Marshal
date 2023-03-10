<?php

namespace App\SocketServer\src;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

// TODO: INCLUDE DOCS FOR SIGNALING SERVER ROUTES

class SignalingServer implements MessageComponentInterface
{

    private array $channels;

    public function __construct()
    {
        $this->channels = array(
            "projects" => array(
                "feedback" => array(),
                "forum" => array()
            ),
            "groups" =>  array(
                "feedback" => array(),
                "forum" => array()
            ),
        );
    }

    function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
    }

    function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
    }
}