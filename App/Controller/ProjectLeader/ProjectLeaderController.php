<?php

namespace App\Controller\ProjectLeader;

use App\Controller\User\UserController;
use App\Model\ProjectLeader;
use App\Model\Project;
use App\Model\Notification;
use App\Model\User;
use App\Model\Task;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectLeaderController extends UserController
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
    public function auth(): bool
    {
        return parent::auth();
    }
    public function getProjectInfo()
    {
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

    public function sendProjectInvitation()
    {
        try {
            // get receiver user name
            $data = file_get_contents('php://input');

            // first check receiver is valid user or not
            // get received user id
            $user = new User();
            $user->readUser("username", $data);
            $receivedUser = $user->getUserData();

            if ($receivedUser) {
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
                "status" => $status
            );

            $task = new Task();
            if ($task->createTask($data)) {
                header("Location: http://localhost/public/user/project?id=" . $_SESSION['project_id']);
            } else {
                echo "Fail";
            }
        } else {
            header("Location: http://localhost/public/user/project?id=" . $_SESSION['project_id']);
        }
    }

    public function sendMessage(){
    }
}
