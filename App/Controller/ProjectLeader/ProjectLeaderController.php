<?php

namespace App\Controller\ProjectLeader;

use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\ProjectLeader;
use App\Model\Project;
use App\Model\Notification;
use App\Model\User;
use App\Model\Task;
use App\Model\Group;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectLeaderController extends ProjectMemberController
{
    private ProjectLeader $projectLeader;
    private Project $project;

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $data = null)
    {
    }

    // in here check the user role whether it is project leader regarding the project
    public function auth()
    {
        return parent::auth();
    }
    public function getProjectInfo(){
        // print_r($_SESSION);
        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;
        $project = new Project($payload->id);

        // print_r($payload);
        $this->sendResponse(
            view: "/project_leader/getProjectInfo.html",
            status: "success",
            content: $project->readProjectsOfUser($user_id, $project_id) ? $project->getProjectData() : array()
        );
    }

    public function sendProjectInvitation(){
        try {
            // get receiver user name
            $data = file_get_contents('php://input');

            // first check receiver is valid user or not
            // get received user id
            $user = new User();
            $user->readUser("username", $data);
            $receivedUser = $user->getUserData();

            if($receivedUser){
                $payload = $this->userAuth->getCredentials();
                $project_id = $_SESSION["project_id"];
                $user_id = $payload->id;

                $date = date("Y-m-d H:i:s");

                $args = array(
                    "projectId" => $project_id,
                    "message" => "Invitation",
                    "type" => "request",
                    "senderId" => $user_id,
                    "sendTime" => $date
                );
            }
            // set notified members
            // get notification id
            $notification = new Notification();
            $notification->createNotification($args);

            $conditions = array(
                "projectId" => $project_id,
                "senderId" => $user_id,
                "sendTime" => $date
            );

            $newNotification = $notification->getNotificationData($conditions);
            $newNotificationId = $newNotification[0]->id;

            $receivedUserId = $receivedUser->id;
            $arguments = array(
                "notificationId" => $newNotificationId,
                "memberId" => $receivedUserId
            );
            $notification->setNotifiedMembers($arguments);

            echo (json_encode(array("message" => "Success")));

        } catch (\Throwable $th) {
            echo (json_encode(array("message" => $th)));
        }
    }

    public function createTask($args){

        if($args['taskname'] && $args['taskdescription']){
            $status = "TO-DO";
            if($args['assignedMember']){
                $user = new User();
                $user->readUser("username", $args['assignedMember']);
                $receivedUser = $user->getUserData();

                if($receivedUser){
                    $conditions = array(
                        "project_id" => $_SESSION['project_id'],
                        "member_id" => $receivedUser->id
                    );
                    
                    if($user->isUserJoinedToProject($conditions)){
                        $status = "ONGOING";

                        // get leader id
                        $payload = $this->userAuth->getCredentials();
                        $user_id = $payload->id;
                        $date = date("Y-m-d H:i:s");

                        // now we have to send a notification as well 
                        $notificationArgs = array(
                            "projectId" => $_SESSION['project_id'],
                            "message" => "You are assigned to ".$args['taskname']. " by project leader",
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
                "status" => $status
            );

            $task = new Task();
            if($task->createTask($data)){
                header("Location: http://localhost/public/user/project?id=".$_SESSION['project_id']);
            }else{
                echo "Fail";
            }
        }else{
            header("Location: http://localhost/public/user/project?id=".$_SESSION['project_id']);
        }
    }

    public function rearangeTask(){
        $data = json_decode(file_get_contents('php://input'));
        $project_id = $_SESSION["project_id"];

        $args = array(
            "status" => $data->new_board,
            "project_id" => $project_id,
            "task_name" => $data->task_name
        );
        $conditions = array("project_id", "task_name");
        $updates = array("status");

        $task = new Task();
        $message = "";

        if($data->new_board === "TO-DO"){
            $args['memberId'] = NULL;
            $updates[] = "memberId";
        }
        try {
            $message = $task->updateTask($args, $updates, $conditions);
            // $message = "Successfully rearraged the task";
        } catch (\Throwable $th) {
            $message = "Failed to rearange the task";
        }
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $message
            ]
        );
    }

    public function assignTask(){
        $data = json_decode(file_get_contents('php://input'));

        $user = new User();
        $user->readUser("username", $data->member_username);
        $receivedUser = $user->getUserData();

        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;

        if($receivedUser){
            $args = array(
                "status" => "ONGOING",
                "memberId" => $receivedUser->id,
                "project_id" => $project_id,
                "task_name" => $data->task_name
            );

            $task = new Task();
            $message = "";

            try {
                $task->pickupTask($args);
                $message = "Successfully picked";

                // send notification to leader
                $date = date("Y-m-d H:i:s");

                // now we have to send a notification as well 
                $notificationArgs = array(
                    "projectId" => $project_id,
                    "message" => "Leader assigned you to " . $data->task_name . '.',
                    "type" => "notification",
                    "senderId" => $user_id,
                    "sendTime" => $date
                );
                $notification = new Notification();
                $notification->createNotification($notificationArgs);
                
                $notifyConditions = array(
                    "projectId" => $project_id,
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

            } catch (\Throwable $th) {
                $message = "Failed to pick up";
            }

            $this->sendJsonResponse(
                status: "success",
                content: [
                    "message" => $message
                ]
            );
        }
    }

    public function createGroup(){
        $data = $_POST;
        var_dump($data);
        $project_id = $_SESSION['project_id'];

        $payload = $this->userAuth->getCredentials();
        $user_id = $payload->id;

        $args = array(
            "group_name" => $data['group_name'],
            "task_name" => $data['task_name'],
            "description" => $data['group_description'],
            "project_id" => $project_id,
            "leader_id" => $user_id
        );
        $keys = array("group_name", "task_name", "description", "project_id", "leader_id");

        $group = new Group();
        $message = "";
        try {
            $group->createGroup($args, $keys);
            $message = "Successfully created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $message
            ]
        );
    }
}

