<?php

namespace App\SocketServer\src;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Core\Token;

class Messenger implements MessageComponentInterface
{
    private array $channels;

    public function __construct()
    {
        $this->channels = array(
            "clients" => array(),
            "projects" => array(),
            "groups" =>  array(),
            "feedback" => array(),
        );
    }

    function onOpen(ConnectionInterface $conn)
    {
        // ws://localhost:8080/projects?route=projectId&ws_token=token
        // ws://localhost:8080/groups?route=projectId&ws_token=token
        // ws://localhost:8080/feedback?route=projectId&ws_token=token

        // var_dump($conn->httpRequest->getUri()->getPath()); // if you used the above url they will get "/projects" as the output
        // var_dump($conn->httpRequest->getUri()->getQuery()); // if you used this then you will get "route=projectId" as the output
        // var_dump($conn->httpRequest->getUri()); check this for more details of what you can get using the getUri()
        // var_dump($conn->httpRequest->getUri()->getRoute());

        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);
        // find which category does the conversation belong to
        if (array_key_exists("ws_token", $args) && $this->authenticate($args["ws_token"])) {
            if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
                // in a particular channel which project or which group does the client belongs to
                // if the project(or the group) forum is not yet initialized then create a new instance of the that forum
                // add the client to that forum
                // forums are usually constructed under their ids projectId, groupId, and so on
                if (array_key_exists("forum", $args)) {
                    // check whether the given project name and the group name exists in the database
                    // create a new forum
                    if (!array_key_exists($args["forum"], $this->channels[$args["category"]])) {
                        $this->channels[$args["category"]][$args["forum"]] = array(
                            "clients" => new \SplObjectStorage(),
                            "client_count" => 0
                        );
                    }
                    // add the new client to the messaging forum
                    $this->channels[$args["category"]][$args["forum"]]["clients"]->attach($conn);
                    // increase the client count
                    $this->channels[$args["category"]][$args["forum"]]["client_count"]++;
                    // var_dump($this->channels[$args["category"]][$args["forum"]]["client_count"]);
                    $conn->send(json_encode(array("status" => "success", "message" => "connection established")));
                } else {
                    $conn->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
                }
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "User cannot be identified")));
        }
    }

    function onClose(ConnectionInterface $conn)
    {
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);
        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("forum", $args) && isset($args["forum"], $this->channels)) {
                // remove the client from the messaging forum
                $this->removeTheClientFromTheMessagingForum($args, $conn);

                if ($this->channels[$args["category"]][$args["forum"]]["client_count"] == 0) {
                    unset($this->channels[$args["category"]][$args["forum"]]);
                    $conn->send(json_encode(array("status" => "success", "message" => "Message closed, since there are no active clients")));
                }
                $conn->send(json_encode(array("status" => "success", "message" => "Client successfully disconnected")));
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        //var_dump($args);
        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("forum", $args) && isset($args["forum"], $this->channels)) {
                // remove the client from the messaging forum
                $this->removeTheClientFromTheMessagingForum($args, $conn);
                if ($this->channels[$args["category"]][$args["forum"]]["client_count"] == 0) {
                    unset($this->channels[$args["category"]][$args["forum"]]);
                }
            } else {
                $conn->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $conn->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
        $conn->send(json_encode(array("status" => "error", "message" => "An error occurred")));
        throw $e;
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        $args = [];
        parse_str($from->httpRequest->getUri()->getQuery(), $args);
        var_dump($args);

        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("forum", $args) && isset($args["forum"], $this->channels)) {
                foreach ($this->channels[$args["category"]][$args["forum"]]["clients"] as $to) {
                    if ($from != $to) {
                        $to->send($msg);
                    }
                }
            } else {
                $from->send(json_encode(array("status" => "error", "message" => "Invalid forum")));
            }
        } else {
            $from->send(json_encode(array("status" => "error", "message" => "Invalid category")));
        }
    }

    /**
     * @param array $args
     * @param ConnectionInterface $conn
     * @return void
     */
    private function removeTheClientFromTheMessagingForum(array $args, ConnectionInterface $conn): void
    {
        $this->channels[$args["category"]][$args["forum"]]["clients"]->detach($conn);
        $this->channels[$args["category"]][$args["forum"]]["client_count"]--;
    }

    private function authenticate(string $token): bool
    {
        // for now just validate the token
        // but have to check the database as well in the future
        return Token::validateToken($token);
    }
}
