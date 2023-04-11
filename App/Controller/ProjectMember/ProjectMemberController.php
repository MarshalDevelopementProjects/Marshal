<?php

namespace App\Controller\ProjectMember;

use App\Controller\User\UserController;
use App\Controller\Group\GroupController;
use App\Controller\Message\MessageController;
use App\Controller\Notification\NotificationController;
use App\Model\ProjectMember;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Project;
use App\Model\Group;
use App\Model\User;
use App\Model\Message;
use Core\Validator\Validator;
use Exception;
use Throwable;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectMemberController extends UserController
{
    private ProjectMember $projectMember;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("project_id", $_SESSION)) {
                $this->projectMember = new ProjectMember($_SESSION["project_id"]);
            } else {
                throw new Exception("Bad request missing arguments");
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $data = null)
    {
    }

    public function auth(): bool
    {
        return parent::auth();
    }

    public function pickupTask()
    {

        $data = json_decode(file_get_contents('php://input'));

        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;

        $task = new Task();
        $project = new Project($user_id);
        $notification = new Notification();

        $args = array(
            "status" => "ONGOING",
            "memberId" => $user_id,
            "project_id" => $project_id,
            "task_name" => $data->task_name
        );
        $updates = array("status", "memberId");
        $conditions = array("project_id", "task_name");

        $message = "";

        try {
            $task->updateTask($args, $updates, $conditions);
            $message = "Successfully picked";

            // send notification to leader
            $date = date("Y-m-d H:i:s");

            // // now we have to send a notification as well 
            $notificationArgs = array(
                "projectId" => $project_id,
                "message" => "I pickup " . $data->task_name . ".",
                "type" => "notification",
                "senderId" => $user_id,
                "sendTime" => $date,
                "url" => "http://localhost/public/user/project?id=" . $project_id
            );
            $notification->createNotification($notificationArgs, array("projectId", "message", "type", "senderId", "sendTime", "url"));
            
            $notifyConditions = array(
                "projectId" => $project_id,
                "senderId" => $user_id,
                "sendTime" => $date
            );
            $newNotification = $notification->getNotification($notifyConditions, array("projectId", "senderId", "sendTime"));

            $thisProject = $project->getProject(array('id' => $project_id));

            $notifyMemberArgs = array(
                "notificationId" => $newNotification->id,
                "memberId" => $thisProject->created_by
            );
            $notification->setNotifiers($notifyMemberArgs, array("notificationId", "memberId"));

            // set task refference 
            $pickupedTask = $task->getTask(array("project_id" => $project_id, "task_name" => $data->task_name), array("project_id","task_name"));
            $notification->addTaskRefference(array("notification_id" => $newNotification->id, "task_id" => $pickupedTask->task_id), array("notification_id", "task_id"));
        } catch (\Throwable $th) {
            $message = "Failed to pick up | " . $th->getMessage();
            // throw $th;
        }

        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $message
            ]
        );
    }

    public function sendConfirmation()
    {
        $data = json_decode(file_get_contents('php://input'));

        $projectId = $_SESSION['project_id'];
        $payload = $this->userAuth->getCredentials();
        $user_id = $payload->id;

        $task = new Task();
        $notification = new Notification();
        $project = new Project($user_id);

        $taskArgs = array(
            "project_id" => $projectId,
            "task_name" => $data->task_name
        );
        $taskData = $task->getTask($taskArgs, array("project_id", "task_name"));
        // $taskData = $task->getTask($taskArgs);

        if ($taskData) {
            $taskId = $taskData->task_id;
        }

        $args = array(
            "taskId" => $taskId,
            "confirmation_type" => $data->confirmation_type,
            "confirmation_message" => $data->confirmation_message,
            "date" => $data->date,
            "time" => $data->time
        );
        $task->completeTask($args);

        // change the state of the task
        $args = array(
            "status" => "REVIEW",
            "project_id" => $projectId,
            "task_name" => $data->task_name
        );
        $task->updateTask($args, array("status"), array("project_id", "task_name"));

        // send notification to infor the project leader
        try {
            $date = date("Y-m-d H:i:s");

            // // now we have to send a notification as well 
            $notificationArgs = array(
                "projectId" => $projectId,
                "message" => $data->confirmation_message,
                "type" => "notification",
                "senderId" => $user_id,
                "sendTime" => $date,
                "url" => "http://localhost/public/user/project?id=" . $projectId
            );
            $notification->createNotification($notificationArgs, array("projectId", "message", "type", "senderId", "sendTime", "url"));
            
            $notifyConditions = array(
                "projectId" => $projectId,
                "senderId" => $user_id,
                "sendTime" => $date
            );
            $newNotification = $notification->getNotification($notifyConditions, array("projectId", "senderId", "sendTime"));

            $thisProject = $project->getProject(array('id' => $projectId));

            $notifyMemberArgs = array(
                "notificationId" => $newNotification->id,
                "memberId" => $thisProject->created_by
            );
            $notification->setNotifiers($notifyMemberArgs, array("notificationId", "memberId"));

            // set task refference 
            $completedTask = $task->getTask(array("project_id" => $projectId, "task_name" => $data->task_name), array("project_id","task_name"));
            $notification->addTaskRefference(array("notification_id" => $newNotification->id, "task_id" => $completedTask->task_id), array("notification_id", "task_id"));

        } catch (\Throwable $th) {
            throw $th;
        }
        
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => "OK!"
            ]
        );
    }

    public function goToGroup(array $data)
    {

        try {
            $payload = $this->userAuth->getCredentials(); // get the payload content
            $project = new Project($payload->id);
            $group = new Group();
            $task = new Task();

            if ($project->readUserRole(member_id: $payload->id, project_id: $_SESSION['project_id'])) {

                // check the user role in the group and redirect him/her to the correct project page
                $_SESSION["group_id"] = $data["id"];

                $args = array(
                    "group_id" => $data['id'],
                    "member_id" => $payload->id
                );
                switch ($group->getGroupMember($args, array("group_id", "member_id"))->role) {
                    case 'LEADER':
                        $args = array(
                            "group_id" => $data['id'],
                            "project_id" => $_SESSION['project_id'],
                            "task_type" => "group"
                        );
                        // $projectController = new ProjectController();
                        $groupController = new GroupController();

                        $groupData = array();
                        $groupData['groupTasks'] = $groupController->getGroupTasks($args, $payload->id);

                        // get group details
                        $groupinfo = $group->getGroup(array("id" => $data['id']), array("id"));
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
                        if ($user->readUser("id", $payload->id)) {
                            $userData = $user->getUserData();
                        }
                        $groupData['userDetails'] = $userData->profile_picture;
                        $groupData['projectDetails'] = $project->getProject(array("id" => $_SESSION['project_id']))->project_name;

                        $groupData += parent::getTaskDeadlines();

                        $this->sendResponse(
                            view: "/group_leader/dashboard.html",
                            status: "success",
                            content: $groupData
                        );
                        break;

                    case 'MEMBER':

                        $args = array(
                            "group_id" => $data['id'],
                            "project_id" => $_SESSION['project_id'],
                            "task_type" => "group"
                        );
                        // $projectController = new ProjectController();
                        $groupController = new GroupController();

                        $this->sendResponse(
                            view: "/group_member/dashboard.html",
                            status: "success",
                            content: $groupController->getGroupTasks($args, $payload->id)
                        );
                        break;
                    default: {
                            unset($_SESSION["project_id"]);
                            $this->sendResponse(
                                view: "/errors/403.html",
                                status: "unauthorized",
                                content: array("message" => "User cannot access this project")
                            );
                        }
                }
            } else {
                $this->sendResponse(
                    view: "/errors/404.html",
                    status: "error",
                    content: array("message" => "User cannot access this project")
                );
            }
            exit;
        } catch (\Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    /**
     * @throws Exception
     */
    public function getForum()
    {
        $this->sendResponse(
            view: "/project_member/forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
                "messages" => $this->projectMember->getForumMessages() ? $this->projectMember->getMessageData() : []
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function getProjectInfo()
    {
        // print_r($_SESSION);
        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;
        $project = new Project($payload->id);

        // get all data related to the project

        $group = new Group();
        $groups = $group->getAllGroups(array("project_id" => $project_id), array("project_id"));
        foreach($groups as $groupData){
            if($group->getGroupMember(array("group_id" => $groupData->id, "member_id" => $user_id), array("group_id", "member_id"))){
                $groupData->hasAccess = true;
            }else {
                $groupData->hasAccess = false;
            }
        }

        $user = new User();
        $data = array("groups" => $groups, "projectData" => $project->getProject(array("id" => $project_id)));

        // get project members' details
        $projectLeaderCondition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id AND role = :role)";
        $projectMemberCondition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id)";
        $groupLeaderCondition = "WHERE id IN (SELECT DISTINCT leader_id FROM groups WHERE project_id = :project_id)";

        $data['projectLeader'] = $user->getAllUsers(array("project_id" => $project_id, "role" => "LEADER"), $projectLeaderCondition);
        $data['projectMembers'] = $user->getAllUsers(array("project_id" => $project_id), $projectMemberCondition);
        $data['groupLeaders'] = $user->getAllUsers(array("project_id" => $project_id), $groupLeaderCondition);

        $data += parent::getTaskDeadlines();

        $this->sendResponse(
            view: "/project_member/getProjectInfo.html",
            status: "success",
            content: $project->readProjectsOfUser($user_id, $project_id) ? $data : array()
        );
    }

    // the following functions sends JSON responses

    // save the message to the project table
    // $args format {"message" => "message string"}
    public function postMessageToProjectForum(array|object $args)
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // get the user id
        if (!empty($args) && array_key_exists("message", $args)) {
            if (!empty($args["message"])) {
                try {
                    if ($this->projectMember->saveForumMessage(id: $this->user->getUserData()->id, msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("internal_server_error", ["message" => "Message cannot be saved!"]);
                    }
                } catch (Exception $exception) {
                    throw $exception;
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
            }
        } else {
            $this->sendJsonResponse("error", ["message" => "Bad request"]);
        }
    }

    // get all the messages to the project table
    public function getProjectForumMessages()
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if ($this->projectMember->getForumMessages()) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectMember->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // save the message to the task table
    // $args format {"task_id" => "TaskID", "message" => "message string"}
    public function postMessageToProjectTaskFeedback(array|object $args)
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if (!empty($args) && array_key_exists("message", $args) && array_key_exists("task_id", $args)) {
                if (!empty($args["message"])) {
                    if ($this->projectMember->saveProjectTaskFeedbackMessage(id: $this->user->getUserData()->id, task_id: $args["task_id"], msg: $args["message"])) {
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

    // $args format {"task_id" => "TaskID"}
    public function getProjectTaskFeedbackMessages(array $args)
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (array_key_exists("task_id", $args)) {
                if ($this->projectMember->getProjectTaskFeedbackMessages($args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectMember->getMessageData() ?? []]);
                } else {
                    $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid input format"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function sendTaskFeedback()
    {
        $data = json_decode(file_get_contents('php://input'));

        $successMessage = "";
        $payload = $this->userAuth->getCredentials();

        $messageController = new MessageController();
        $message = new Message();
        $task = new Task();
        $project = new Project($payload->id);

        $date = date('Y-m-d H:i:s');
        $args = array(
            "sender_id" => $payload->id,
            "stamp" => $date,
            "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE",
            "msg" => $data->feedbackMessage
        );
        try {
            $messageController->send($args, array("sender_id", "stamp", "message_type", "msg"));

            $newMessage = $message->getMessage(array("sender_id" => $payload->id, "stamp" => $date, "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE"), array("sender_id", "stamp", "message_type"));
            $messageTypeArgs = array(
                "message_id" => $newMessage->id,
                "project_id" => $_SESSION['project_id'],
                "task_id" => $data->task_id
            );

            $message->setMessageType($messageTypeArgs, array("message_id", "project_id", "task_id"), "project_task_feedback_message");
            $successMessage = "Message sent successfully";
        } catch (\Throwable $th) {
            $successMessage = "Message sent failed | " . $th->getMessage();
        }

        // set the reciver of the message
        $thisProject = $project->getProject(array('id' => $_SESSION['project_id']));
        $reciverId = $thisProject->created_by;

        $thisTask = $task->getTask(array("task_id" => $data->task_id), array("task_id"));
        if($thisTask->memberId != $payload->id){
            $reciverId = $thisTask->memberId;
        }
        // send notification to reciever
        try {
            $notification = new Notification();
            $date = date("Y-m-d H:i:s");

            // // now we have to send a notification as well 
            $notificationArgs = array(
                "projectId" => $_SESSION['project_id'],
                "message" => $data->feedbackMessage,
                "type" => "notification",
                "senderId" => $payload->id,
                "sendTime" => $date,
                "url" => "http://localhost/public/user/project?id=" . $_SESSION['project_id']
            );
            $notification->createNotification($notificationArgs, array("projectId", "message", "type", "senderId", "sendTime", "url"));
            
            $notifyConditions = array(
                "projectId" => $_SESSION['project_id'],
                "senderId" => $payload->id,
                "sendTime" => $date
            );
            $newNotification = $notification->getNotification($notifyConditions, array("projectId", "senderId", "sendTime"));

            $notifyMemberArgs = array(
                "notificationId" => $newNotification->id,
                "memberId" => $reciverId
            );
            $notification->setNotifiers($notifyMemberArgs, array("notificationId", "memberId"));
            $notification->addTaskRefference(array("notification_id" => $newNotification->id, "task_id" => $data->task_id), array("notification_id", "task_id"));

        } catch (\Throwable $th) {
            throw $th;
        }
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
            ]
        );
    }

    public function getTaskFeedback(){

        try {
            $task_id = $_GET['task'];
            $messageController = new MessageController();

            $condition = "id IN(SELECT message_id FROM `project_task_feedback_message` WHERE task_id =" . $task_id . " AND project_id = " . $_SESSION['project_id'] .") ORDER BY `stamp` LIMIT 100";
            $feedbackMessages = $messageController->recieve($condition);

            foreach ($feedbackMessages as $feedback) {
                if($feedback->sender_id != $this->user->getUserData()->id){
                    $user = new User();
                    $user->readUser("id", $feedback->sender_id);

                    $sender = $user->getUserData();
                    $feedback->profile = $sender->profile_picture;
                    $feedback->type = "incoming";
                }else{
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
    public function getProjectAnnouncements(){
        $messageController = new MessageController();
        $message = new Message();
        $user = new User();

        $condition = "id IN(SELECT message_id FROM `project_announcement` WHERE project_id = " . $_SESSION['project_id'] .") ORDER BY `stamp` LIMIT 100";
    
        $announcements = $messageController->recieve($condition);
        foreach($announcements as $announcement){
            // add sender profile and announcement heading
            $sender = $user->readMember("id", $announcement->sender_id);
            $announcement->profile = $sender->profile_picture;

            $headingCondition = "project_id = " . $_SESSION['project_id'] . " AND message_id = " . $announcement->id;
            $announcement->heading = $message->getAnnouncementHeading($headingCondition, 'project_announcement')->heading;
            $announcement->senderType = 'project leader';
        }
        
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $announcements
            ]
        );
    }
}
