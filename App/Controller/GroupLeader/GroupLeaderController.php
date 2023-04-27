<?php

namespace App\Controller\GroupLeader;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\GroupLeader;
use App\Model\User;
use App\Model\Notification;
use App\Model\Task;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupLeaderController extends ProjectMemberController
{
    private GroupLeader $groupLeader;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("group_id", $_SESSION)) {
                $this->groupLeader = new GroupLeader($_SESSION["project_id"], $_SESSION["group_id"]);
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

    public function createTask($args)
    {
        if ($args['taskname'] && $args['taskdescription']) {
            $status = "TO-DO";
            if ($args['assignedMember']) {
                $user = new User();
                $user->readUser("username", $args['assignedMember']);
                $receivedUser = $user->getUserData();

                if ($receivedUser) {
                    $conditions = array(
                        "project_id" => $_SESSION['project_id'],
                        "member_id" => $receivedUser->id
                    );

                    if ($user->isUserJoinedToProject($conditions)) {
                        $status = "ONGOING";

                        // get leader id
                        $payload = $this->userAuth->getCredentials();
                        $user_id = $payload->id;
                        $date = date("Y-m-d H:i:s");

                        // now we have to send a notification as well 
                        $notificationArgs = array(
                            "projectId" => $_SESSION['project_id'],
                            "message" => "You are assigned to " . $args['taskname'] . " by project leader",
                            "type" => "notification",
                            "senderId" => $user_id,
                            "sendTime" => $date
                        );
                        $notification = new Notification();
                        $notification->createNotification($notificationArgs);

                        $notifyConditions = array(
                            "projectId" => $_SESSION['project_id'],
                            "senderId" => $user_id,
                            "sendTime" => $date
                        );
                        $newNotification = $notification->getNotificationData($notifyConditions);
                        $newNotificationId = $newNotification[0]->id;

                        $notifyMemberArgs = array(
                            "notificationId" => $newNotificationId,
                            "memberId" => $receivedUser->id
                        );
                        $notification->setNotifiedMembers($notifyMemberArgs);
                    }
                }
            }

            $data = array(
                "project_id" => $_SESSION['project_id'],
                "description" => $args['taskdescription'],
                "deadline" => $args['taskdeadline'],
                "task_name" => $args['taskname'],
                "priority" => $args['priority'],
                "status" => $status,
                "task_type" => "group"
            );

            $task = new Task();
            if ($task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status", "task_type"))) {

                $newTask = $task->getTask(array("project_id" => $_SESSION['project_id'], "task_name" => $args['taskname']), array("project_id", "task_name"));
                $task->addGroupToTask(array("task_id" => $newTask->task_id, "group_id" => $_SESSION['group_id']));

                header("Location: http://localhost/public/projectmember/group?id=" . $_SESSION['group_id']);
            } else {
                echo "Fail";
            }
        } else {
            header("Location: http://localhost/public/projectmember/group?id=" . $_SESSION['group_id']);
        }
    }

    /**
     * @throws Exception
     */
    public function getGroupInfo()
    {
        $this->sendResponse(
            view: "/group_leader/groupInfo.html",
            status: "success",
            content: [
                "messages" => $this->groupLeader->getGroupFeedbackForumMessages(project_id: $_SESSION["project_id"]) ? $this->groupLeader->getMessageData() : [],
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function getForum(): void
    {
        $this->sendResponse(
            view: "/group_leader/forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "group_id" => $_SESSION["group_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture],
                "messages" => $this->groupLeader->getGroupForumMessages(project_id: $_SESSION["project_id"]) ? $this->groupLeader->getMessageData() : [],
                "members" =>  $this->groupLeader->getGroupMembers() ? $this->groupLeader->getGroupMemberData() : [],
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function getGroupFeedbackForum(): void
    {
        $this->sendResponse(
            view: "/group_leader/feedback_forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "group_id" => $_SESSION["group_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture],
                "messages" => $this->groupLeader->getGroupFeedbackForumMessages(project_id: $_SESSION["project_id"]) ? $this->groupLeader->getMessageData() : [],
            ]
        );
    }

    // $args = ["message" => "message string"];
    public function postMessageToGroupFeedback(array|object $args)
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->groupLeader->saveGroupFeedbackMessage(id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("error", ["message" => "No such group!"]);
                    }
                } else {
                    $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid request  format!"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupFeedbackMessages()
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if ($this->groupLeader->getGroupFeedbackForumMessages($_SESSION["project_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->groupLeader->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // $args must follow this format
    // ["message" => "content of the message"]
    public function postMessageToGroupForum(array $args)
    {
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->groupLeader->saveGroupMessage(id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], msg: $args["message"])) {
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
            if ($this->groupLeader->getGroupForumMessages(project_id: $_SESSION["project_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->groupLeader->getMessageData() ?? []]);
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
                    if ($this->groupLeader->saveGroupTaskFeedbackMessage(id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], task_id: $args["task_id"], msg: $args["message"])) {
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
                if ($this->groupLeader->getGroupTaskFeedbackMessages(project_id: $_SESSION["project_id"], task_id: $args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->groupLeader->getMessageData() ?? []]);
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
