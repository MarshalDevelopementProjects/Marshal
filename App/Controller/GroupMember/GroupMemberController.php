<?php

namespace App\Controller\GroupMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\GroupMember;
use Core\Validator\Validator;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupMemberController extends ProjectMemberController
{
    private GroupMember $groupMember;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("group_id", $_SESSION)) {
                $this->groupMember = new GroupMember($_SESSION["project_id"], $_SESSION["group_id"]);
            } else {
                throw new Exception("Bad request missing arguments");
            }
        } catch (Exception $exception) {
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
    public function getGroupInfo()
    {
        $this->sendResponse(
            view: "/group_member/groupInfo.html",
            status: "success",
        );
    }

    // $args must follow this format
    // ["message" => "content of the message"]
    public function postMessageToGroupForum(array $args)
    {
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->groupMember->saveGroupMessage(id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("error", ["message" => "Message cannot be saved to the database"]);
                    }
                } else {
                    $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid request  format!"]);
            }
        } catch (Exception $exception) {
            throw  $exception;
        }
    }

    public function getGroupForumMessages()
    {
        try {
            if ($this->groupMember->getGroupMessages(project_id: $_SESSION["project_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->groupMember->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // $args must follow this format
    // ["task_id" => "TaskID", "message" => "content of the message"]
    public function postMessageToGroupTaskFeedback(array $args)
    {
        try {
            if (!empty($args) && array_key_exists("message", $args) && array_key_exists("task_id", $args)) {
                if (!empty($args["message"]) && !empty($args["task_id"])) {
                    if ($this->groupMember->saveGroupTaskFeedbackMessage(id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], task_id: $args["task_id"], msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("error", ["message" => "Message cannot be saved to the database"]);
                    }
                } else {
                    $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid request  format!"]);
            }
        } catch (Exception $exception) {
            throw  $exception;
        }
    }

    // $args must follow this format
    // ["task_id" => "TaskID", "message" => "content of the message"]
    public function getGroupTaskFeedbackMessages(array $args)
    {
        try {
            if (array_key_exists("task_id", $args) && !empty($args["task_id"])) {
                if ($this->groupMember->getGroupTaskFeedbackMessages(project_id: $_SESSION["project_id"], task_id: $args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->groupMember->getMessageData() ?? []]);
                } else {
                    $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid request  format!"]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
