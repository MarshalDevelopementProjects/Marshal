<?php

namespace App\Controller\GroupLeader;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Controller\Notification\NotificationController;
use App\Controller\Message\MessageController;
use App\Model\GroupLeader;
use App\Model\User;
use App\Model\Project;
use App\Model\Message;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Group;
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
                        
                        $notification = new Notification();
                        $notificationController = new NotificationController();
            
                        $args = array(
                            "message" => "You are assigned to " . $args['taskname'] . " by project leader",
                            "type" => "notification",
                            "sender_id" => $user_id,
                            "url" => "Location: http://localhost/public/projectmember/group?id=" . $_SESSION['group_id'],
                            "recive_id" => $receivedUser->id
                        );
                        
                        $notificationId = $notificationController->setNotification($args);
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

    public function getGroupInfo()
    {

        $payload = $this->userAuth->getCredentials();
        
        $group = new Group();
        $project = new Project($payload->id);
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
        if($user->readUser("id", $payload->id)){
            $userData = $user->getUserData();
        }
        $groupData['userDetails'] = $userData->profile_picture;
        $groupData['projectDetails'] = $project->getProject(array("id" => $_SESSION['project_id']))->project_name;

        // get group members
        $condition = "WHERE id IN (SELECT member_id FROM group_join WHERE group_id = :group_id AND role = :role)";
        $groupMemberCondition = "WHERE id IN (SELECT member_id FROM group_join WHERE group_id = :group_id)";

        $groupData['groupLeader'] = $user->getAllUsers(array("group_id" => $_SESSION['group_id'], "role" => "LEADER"), $condition);
        $groupData['groupMembers'] = $user->getAllUsers(array("group_id" => $_SESSION['group_id']), $groupMemberCondition);

        $groupData += parent::getTaskDeadlines();

        $this->sendResponse(
            view: "/group_leader/groupInfo.html",
            status: "success",
            content: $groupData
        );
    }

    public function addAnnouncement(){
        $data = json_decode(file_get_contents('php://input'));

        $successMessage = "";
        $payload = $this->userAuth->getCredentials();
        $messageController = new MessageController();
        $message = new Message();
        $notification =  new Notification();
        $project = new Project($payload->id);

        $date = date('Y-m-d H:i:s');
        $args = array(
            "sender_id" => $payload->id,
            "stamp" => $date,
            "message_type" => "GROUP_ANNOUNCEMENT",
            "msg" => $data->announcementMessage
        );
        try {
            $messageController->send($args, array("sender_id", "stamp", "message_type", "msg"));

            $newMessage = $message->getMessage(array("sender_id" => $payload->id, "stamp" => $date, "message_type" => "GROUP_ANNOUNCEMENT"), array("sender_id", "stamp", "message_type"));
            // var_dump($newMessage);
            $messageTypeArgs = array(
                "message_id" => $newMessage->id,
                "project_id" => $_SESSION['project_id'],
                "heading" => $data->announcementHeading
            );

            $message->setMessageType($messageTypeArgs, array("message_id", "project_id", "heading"), "group_announcement");

            // send notifications
            try {
                $notification = new Notification();
                $group = new Group();
                $notificationController = new NotificationController();
    
                $args = array(
                    "message" => $data->announcementHeading,
                    "type" => "notification",
                    "sender_id" => $payload->id,
                    "url" => "http://localhost/public/projectmember/group?id=" . $_SESSION['group_id'],
                    "recive_id" => null
                );
                
                $notificationId = $notificationController->setNotification($args);
                
                $condition = "WHERE id IN (SELECT member_id FROM group_join WHERE group_id = :group_id)";
                $members = $user->getAllUsers(array("group_id" => $_SESSION['group_id']), $groupMemberCondition);
                
                $notificationController->boardcastNotification($notificationId, $members);
    
            } catch (\Throwable $th) {
                $successMessage = $th->getMessage();
            }

            $successMessage = "Message sent successfully";
        } catch (\Throwable $th) {
            $successMessage = "Message sent failed";
            // throw $th;
        }

        
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
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
            if ($this->groupLeader->getGroupFeedbackMessages($_SESSION["project_id"])) {
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
            if ($this->groupLeader->getGroupMessages(project_id: $_SESSION["project_id"])) {
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
