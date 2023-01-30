<?php

namespace App\Controller\ProjectMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\ProjectMember;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Project;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectMemberController extends UserController
{
    private ProjectMember $projectMember;

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

    public function auth()
    {
        return parent::auth();
    }

    public function pickupTask(){

        $data = json_decode(file_get_contents('php://input'));
        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;

        $args = array(
            "status" => $data->state,
            "memberId" => $user_id,
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
                "message" => "I pickup ". $data->task_name . ".",
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
                "memberId" => 1
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
