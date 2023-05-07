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
    private array $channels;

    public function __construct()
    {
        $this->channels = array(
            "projects" => array(
                "feedback" => array(),
                "forum" => array()
            ),
        );
    }

    function onOpen(ConnectionInterface $conn): void
    {
        $args = [];
        var_dump($args);
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);

        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("route", $args) && isset($args["route"], $this->channels[$args["category"]])) {
                if ($args["category"] == "projects") {
                    {
                        if (!array_key_exists($args["project"], $this->channels[$args["category"]][$args["route"]])) {
                            $this->channels[$args["category"]][$args["route"]][$args["project"]] = array(
                                "clients" => new \SplObjectStorage(),
                                "client_count" => 0
                            );
                        }
                        $this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"]->attach($conn);
                        $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"]++;
                    }
                }
                $conn->send(json_encode(array("status" => "success", "message" => "Connection established")));
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid route")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
    }

    function onClose(ConnectionInterface $conn): void
    {
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);
        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("route", $args) && isset($args["route"], $this->channels[$args["category"]])) {
                // remove the client from the messaging forum
                $this->removeClient($args, $conn);
                $conn->send(json_encode(array("status" => "success", "message" => "Client successfully disconnected")));
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
    }

    /**
     * @param array $args
     * @param ConnectionInterface $conn
     * @return void
     */
    private function removeTheClientFromTheSession(array $args, ConnectionInterface $conn): void
    {
        if ($args["category"] == "projects") {
            {
                $this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"]->detach($conn);
                $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"]--;
            }
        }
    }

    /**
     * @param array $args
     * @param ConnectionInterface $conn
     * @return void
     */
    private function removeClient(array $args, ConnectionInterface $conn): void
    {
        $this->removeTheClientFromTheSession($args, $conn);
        if ($args["category"] == "projects") {
            {
                if (
                    array_key_exists($args["project"], $this->channels[$args["category"]][$args["route"]]) &&
                    $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"] == 0
                ) {
                    unset($this->channels[$args["category"]][$args["route"]][$args["project"]]);
                    $conn->send(json_encode(array("status" => "success", "message" => "Message closed, since there are no active clients")));
                }
            }
        }
    }

    function onError(ConnectionInterface $conn, Exception $e): void
    {
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        //var_dump($args);
        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("route", $args) && isset($args["route"], $this->channels["category"])) {
                // remove the client from the messaging forum
                $this->removeClient($args, $conn);
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
        $conn->send(json_encode(array("status" => "error", "message" => "An error occurred")));
        throw $e;
    }

    function onMessage(ConnectionInterface $from, $msg): void
    {
        $args = [];
        parse_str($from->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);

        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("route", $args) && isset($args["route"], $this->channels[$args["category"]])) {
                if ($args["category"] == "projects") {
                    {
                        foreach ($this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"] as $to) {
                            if ($from != $to) {
                                $to->send($msg);
                            }
                        }
                    }
                }
            } else {
                $from->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $from->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
    }
}
