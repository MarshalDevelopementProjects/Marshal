<?php

namespace App\Controller\Client;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\Client;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class ClientController extends UserController
{
    private Client $client;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("project_id", $_SESSION)) {
                $this->client = new Client($_SESSION["project_id"]);
            } else {
                throw new Exception("Bad request missing arguments");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
    }

    public function auth(): bool
    {
        return parent::auth();
    }

    // save the message to the project table
    // $args format {"message" => "message string"}
    public function postMessageToProjectFeedback(array|object $args)
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    $id = $this->user->getUserData()->id;
                    if ($this->client->saveProjectFeedbackMessage(id: $id, msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("internal_server_error", ["message" => "Message cannot be saved!"]);
                    }
                } else {
                    $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Bad request"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectFeedbackMessages()
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if ($this->client->getProjectFeedbackMessages()) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->client->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
