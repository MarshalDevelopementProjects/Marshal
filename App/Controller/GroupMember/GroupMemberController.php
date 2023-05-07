<?php

namespace App\Controller\GroupMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Controller\Notification\NotificationController;
use App\Controller\Message\MessageController;
use App\Model\GroupMember;
use App\Model\Message;
use App\Model\User;
use App\Model\Task;
use App\Model\Project;
use App\Model\Notification;
use App\Model\Group;
use Core\Validator\Validator;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupMemberController extends ProjectMemberController
{
    protected Group $group;
    private GroupMember $groupMember;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("group_id", $_SESSION) && $this->user->checkUserRole(req_id: $_SESSION["group_id"], role: "MEMBER", type: "GROUP")) {
                $this->group = new Group();
                $this->groupMember = new GroupMember($_SESSION["project_id"], $_SESSION["group_id"]);
            } else {
                $this->sendResponse(
                    view: "/errors/403.html",
                    status: "unauthorized"
                );
                // throw new Exception("Bad request missing arguments");
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
        // TODO: COMPLETE THE AUTH
        return parent::auth();
    }

    /**
     * @throws \Throwable
     */
    public function getGroupInfo()
    {

        $payload = $this->userAuth->getCredentials();
        $group = new Group();
        $project = new Project();
        $task = new Task();
        $groupData = array();

        // get group details
        $groupinfo = $group->getGroup(array("id" => $_SESSION['group_id']), array("id"));
        $projectinfo = $project->getProject(array("id" => $_SESSION['project_id']));
        $taskinfo = $task->getTask(array("task_name" => $groupinfo->task_name, "project_id" => $_SESSION['project_id']), array("task_name", "project_id"));

        $groupData['groupDetails'] = array(
            "name" => $groupinfo->group_name,
            "description" => $groupinfo->description,
            "start_date" => explode(" ", $groupinfo->start_date)[0],
            "end_date" => explode(" ", $taskinfo->deadline)[0],
            "project_name" => $projectinfo->project_name
        );

        // get user details
        $user = new User();

        $userData = array();
        if ($user->readUser("id", $this->user->getUserData()->id)) {
            $userData = $user->getUserData();
        }
        $groupData['userDetails'] = $userData->profile_picture;
        $groupData['projectDetails'] = $project->getProject(array("id" => $_SESSION['project_id']))->project_name;

        $groupData += parent::getTaskDeadlines();

        $groupData["progress"] = $group->getGroupProgress(group_id: $_SESSION["group_id"]);

        $groupData["user_data"] = [
            "username" => $this->user->getUserData()->username,
            "profile_picture" => $this->user->getUserData()->profile_picture,
        ];

        if($group->getGroupMembers_(group_id: $_SESSION["group_id"])) {
            $groupData["members"] = $group->getGroupMemberData();
        }

        if($group->getGroupStatistics(group_id: $_SESSION["group_id"])) {
            $groupData["stat"] = $group->getGroupData();
        }

        $this->sendResponse(
            view: "/group_member/groupInfo.html",
            status: "success",
            content: $groupData
        );
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function getForum(): void
    {
        $this->sendResponse(
            view: "/group_member/forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "group_id" => $_SESSION["group_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
                "messages" => $this->forum->getGroupForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"]) ? $this->forum->getMessageData() : [],
                "members" =>  $this->group->getGroupMembers_(group_id: $_SESSION["group_id"]) ? $this->group->getGroupMemberData() : [],
            ]
        );
    }

    /**
     * @throws \Throwable
     */
    public function getGroupAnnouncements()
    {
        $messageController = new MessageController();
        $message = new Message();
        $user = new User();

        $condition = "id IN(SELECT message_id FROM `group_announcement` WHERE project_id = " . $_SESSION['project_id'] . " AND group_id = " . $_SESSION['group_id'] . ") ORDER BY `stamp` LIMIT 100";

        $announcements = $messageController->recieve($condition);
        foreach ($announcements as $announcement) {
            // add sender profile and announcement heading
            $sender = $user->readMember("id", $announcement->sender_id);
            $announcement->profile = $sender->profile_picture;

            $headingCondition = "project_id = " . $_SESSION['project_id'] . " AND message_id = " . $announcement->id . " AND group_id = " . $_SESSION['group_id'];
            $announcement->heading = $message->getAnnouncementHeading($headingCondition, 'group_announcement')->heading;
            $announcement->senderType = 'group leader';
        }

        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $announcements
            ]
        );
    }

    /**
     * @throws \Throwable
     */
    public function sendTaskFeedback()
    {
        $data = json_decode(file_get_contents('php://input'));

        $successMessage = "";
        $payload = $this->userAuth->getCredentials();

        $messageController = new MessageController();
        $message = new Message();
        $task = new Task();
        $project = new Project($payload->id);
        $group = new Group();

        $date = date('Y-m-d H:i:s');
        $args = array(
            "sender_id" => $payload->id,
            "stamp" => $date,
            "message_type" => "GROUP_TASK_FEEDBACK_MESSAGE",
            "msg" => $data->feedbackMessage
        );
        try {
            $messageController->send($args, array("sender_id", "stamp", "message_type", "msg"));

            $newMessage = $message->getMessage(array("sender_id" => $payload->id, "stamp" => $date, "message_type" => "GROUP_TASK_FEEDBACK_MESSAGE"), array("sender_id", "stamp", "message_type"));
            $messageTypeArgs = array(
                "message_id" => $newMessage->id,
                "project_id" => $_SESSION['project_id'],
                "task_id" => $data->task_id,
                "group_id" => $_SESSION['group_id']
            );

            $message->setMessageType($messageTypeArgs, array("message_id", "project_id", "task_id", "group_id"), "group_task_feedback_message");
            $successMessage = "Message sent successfully";
        } catch (\Throwable $th) {
            $successMessage = "Message sent failed | " . $th->getMessage();
        }

        // set the reciver of the message

        $thisGroup = $group->getGroup(array("id" => $_SESSION['group_id']), array('id'));
        $reciverId = $thisGroup->leader_id;

        $thisTask = $task->getTask(array("task_id" => $data->task_id), array("task_id"));
        if ($thisTask->member_id != $payload->id) {
            $reciverId = $thisTask->member_id;
        }
        // send notification to reciever
        if ($payload->id != $reciverId) {
            try {
                $notification = new Notification();
                $notificationController = new NotificationController();

                $args = array(
                    "message" => $data->feedbackMessage,
                    "type" => "notification",
                    "sender_id" => $payload->id,
                    "url" => "http://localhost/public/projectmember/group?id=" . $_SESSION['group_id'],
                    "recive_id" => $reciverId
                );

                $notificationId = $notificationController->setNotification($args);
                $notification->addTaskRefference(array("notification_id" => $notificationId, "task_id" => $data->task_id), array("notification_id", "task_id"));
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
            ]
        );
    }

    public function getTaskFeedback()
    {

        try {
            $task_id = $_GET['task'];
            $messageController = new MessageController();

            $condition = "id IN(SELECT message_id FROM `group_task_feedback_message` WHERE task_id =" . $task_id . " AND project_id = " . $_SESSION['project_id'] . " AND group_id = " . $_SESSION['group_id'] . ") ORDER BY `stamp` LIMIT 100";
            $feedbackMessages = $messageController->recieve($condition);

            foreach ($feedbackMessages as $feedback) {
                if ($feedback->sender_id != $this->user->getUserData()->id) {
                    $user = new User();
                    $user->readUser("id", $feedback->sender_id);

                    $sender = $user->getUserData();
                    $feedback->profile = $sender->profile_picture;
                    $feedback->type = "incoming";
                } else {
                    $feedback->profile = null;
                    $feedback->type = "outgoing";
                }
            }

            $this->sendJsonResponse(
                status: "success",
                content: [
                    "message" => $feedbackMessages
                ]
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // $args must follow this format
    // ["message" => "content of the message"]
    public function postMessageToGroupForum(array $args): void
    {
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->forum->saveGroupMessage(sender_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"],msg: $args["message"])) {
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

    public function getGroupForumMessages(): void
    {
        try {
            if ($this->forum->getGroupForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->forum->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => ""]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // $args must follow this format
    // ["task_id" => "TaskID", "message" => "content of the message"]
    public function postMessageToGroupTaskFeedback(array $args): void
    {
        try {
            if (!empty($args) && array_key_exists("message", $args) && array_key_exists("task_id", $args)) {
                if (!empty($args["message"]) && !empty($args["task_id"])) {
                    if ($this->forum->saveGroupTaskFeedbackMessage(sender_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"], task_id: $args["task_id"], msg: $args["message"])) {
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
    public function getGroupTaskFeedbackMessages(array $args): void
    {
        try {
            if (array_key_exists("task_id", $args) && !empty($args["task_id"])) {
                if ($this->forum->getGroupTaskFeedbackMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"], task_id: $args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->forum->getMessageData() ?? []]);
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
