<?php

namespace App\SocketServer\src;

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Messenger implements MessageComponentInterface
{
    private array $channels;

    /*
     *
     * WS Routes
     *
     * +=========+
     * |Structure|
     * +=========+
     *
     *  Projects
     * =========
     *  Base +> ws://localhost:8080/projects/
     *
     *  Project message forum +>  ws://localhost:8080/project/forum?project=ProjectID
     *
     *  Project feedback forum +>  ws://localhost:8080/project/feedback?project=ProjectID
     *
     *  Groups
     * =========
     *  Base +> ws://localhost:8080/groups/
     *
     *  Group message forum +>  ws://localhost:8080/groups/forum?project=ProjectID&group=GroupID
     *
     *  Group feedback forum +>  ws://localhost:8080/groups/feedback?project=ProjectID&group=GroupID
     *
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * |                Route                                                  |                    Route tokens                                     |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * | ws://localhost:8080/project/forum?project=ProjectID                   | routing_params => ['category' => 'project', 'route' => 'forum']     |
     * |                                                                       | http_params => ['project' => '12']                                  |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * | ws://localhost:8080/project/feedback?project=ProjectID                | routing_params => ['category' => 'project', 'route' => 'feedback']  |
     * |                                                                       | http_params => ['project' => '12']                                  |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * | ws://localhost:8080/groups/forum?project=ProjectID&group=GroupID      | routing_params => ['category' => 'groups', 'route' => 'forum']      |
     * |                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * | ws://localhost:8080/groups/feedback?project=ProjectID&group=GroupID   | routing_params => ['category' => 'groups', 'route' => 'feedback']   |
     * |                                                                       | http_params => ['project' => '12', 'group' => '12']                 |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     * | ws://localhost:8080/tasks/feedback?task=TaskID                        | routing_params => ['category' => 'tasks', 'route' => 'feedback']    |
     * |                                                                       | http_params => ['task' => '12']                                     |
     * +-----------------------------------------------------------------------+---------------------------------------------------------------------+
     *  */

    public function __construct()
    {
        $this->channels = array(
            "projects" => array(
                "feedback" => array(),
                "forum" => array()
            ),
            "groups" =>  array(
                "feedback" => array(), // used to keep track of the clients joining for the feedback forums
                "forum" => array() // used to keep track of the clients joining for the general forums
            ),
            "tasks" => array(
                "feedback" => array()
            ),
        );
    }

    function onOpen(ConnectionInterface $conn): void
    {
        $args = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $args);
        // var_dump($args);
        // var_dump($conn->resourceId);

        if (array_key_exists("category", $args) && isset($args["category"], $this->channels)) {
            if (array_key_exists("route", $args) && isset($args["route"], $this->channels[$args["category"]])) {
                switch ($args["category"]) {
                    case "projects": {
                        if (!array_key_exists($args["project"], $this->channels[$args["category"]][$args["route"]])) {
                            $this->channels[$args["category"]][$args["route"]][$args["project"]] = array(
                                "clients" => new \SplObjectStorage(),
                                "client_count" => 0
                            );
                        }
                        $this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"]->attach($conn);
                        $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"]++;
                    }
                        break;
                    case "groups": {
                        if (!array_key_exists($args["group"], $this->channels[$args["category"]][$args["route"]])) {
                            $this->channels[$args["category"]][$args["route"]][$args["group"]] = array(
                                "clients" => new \SplObjectStorage(),
                                "client_count" => 0
                            );
                        }
                        $this->channels[$args["category"]][$args["route"]][$args["group"]]["clients"]->attach($conn);
                        $this->channels[$args["category"]][$args["route"]][$args["group"]]["client_count"]++;
                    }
                        break;
                    case "tasks": {
                        if (!array_key_exists($args["task"], $this->channels[$args["category"]][$args["route"]])) {
                            $this->channels[$args["category"]][$args["route"]][$args["project"]] = array(
                                "clients" => new \SplObjectStorage(),
                                "client_count" => 0
                            );
                        }
                        $this->channels[$args["category"]][$args["route"]][$args["task"]]["clients"]->attach($conn);
                        $this->channels[$args["category"]][$args["route"]][$args["task"]]["client_count"]++;
                    }
                        break;
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

    function onError(ConnectionInterface $conn, \Exception $e)
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
                switch ($args["category"]) {
                    case "projects": {
                        foreach ($this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"] as $to) {
                            if ($from != $to) {
                                $to->send($msg);
                            }
                        }
                    }
                        break;
                    case "groups": {
                        foreach ($this->channels[$args["category"]][$args["route"]][$args["group"]]["clients"] as $to) {
                            if ($from != $to) {
                                $to->send($msg);
                            }
                        }
                    }
                        break;
                    case "tasks": {
                        foreach ($this->channels[$args["category"]][$args["route"]][$args["task"]]["clients"] as $to) {
                            if ($from != $to) {
                                $to->send($msg);
                            }
                        }
                    }
                        break;
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
        switch ($args["category"]) {
            case "projects": {
                $this->channels[$args["category"]][$args["route"]][$args["project"]]["clients"]->detach($conn);
                $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"]--;
            }
                break;
            case "groups": {
                $this->channels[$args["category"]][$args["route"]][$args["group"]]["clients"]->detach($conn);
                $this->channels[$args["category"]][$args["route"]][$args["group"]]["client_count"]--;
            }
                break;
            case "tasks": {
                $this->channels[$args["category"]][$args["route"]][$args["task"]]["clients"]->detach($conn);
                $this->channels[$args["category"]][$args["route"]][$args["task"]]["client_count"]--;
            }
                break;
        }
    }

    /**
     * @param array $args
     * @param ConnectionInterface $conn
     * @return void
     */
    private function removeClient(array $args, ConnectionInterface $conn): void
    {
        $this->removeTheClientFromTheMessagingForum($args, $conn);
        switch ($args["category"]) {
            case "projects": {
                if (
                    array_key_exists($args["project"], $this->channels[$args["category"]][$args["route"]]) &&
                    $this->channels[$args["category"]][$args["route"]][$args["project"]]["client_count"] == 0
                ) {
                    unset($this->channels[$args["category"]][$args["route"]][$args["project"]]);
                    $conn->send(json_encode(array("status" => "success", "message" => "Message closed, since there are no active clients")));
                }
            }
                break;
            case "groups": {
                if (
                    array_key_exists($args["group"], $this->channels[$args["category"]][$args["route"]]) &&
                    $this->channels[$args["category"]][$args["route"]][$args["group"]]["client_count"] == 0
                ) {
                    unset($this->channels[$args["category"]][$args["route"]][$args["group"]]);
                    $conn->send(json_encode(array("status" => "success", "message" => "Message closed, since there are no active clients")));
                }
            }
                break;
            case "tasks": {
                if (
                    array_key_exists($args["task"], $this->channels[$args["category"]][$args["route"]]) &&
                    $this->channels[$args["category"]][$args["route"]][$args["task"]]["client_count"] == 0
                ) {
                    unset($this->channels[$args["category"]][$args["route"]][$args["task"]]);
                    $conn->send(json_encode(array("status" => "success", "message" => "Message closed, since there are no active clients")));
                }
            }
                break;
        }
    }
}