<?php

namespace App\Controller\ProjectLeader;

use App\Controller\User\UserController;
use App\Model\ProjectLeader;
use App\Model\Project;

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
}
