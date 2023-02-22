<?php

namespace App\Controller\ProjectMember;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Controller\Group\GroupController;
use App\Model\ProjectMember;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Project;
use App\Model\Group;
use App\Model\User;
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

    public function auth(): bool
    {
        return parent::auth();
    }

    public function pickupTask(){

        $data = json_decode(file_get_contents('php://input'));
        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;

        $args = array(
            "status" => "ONGOING",
            "memberId" => $user_id,
            "project_id" => $project_id,
            "task_name" => $data->task_name
        );
        $updates = array("status", "memberId");
        $conditions = array("project_id", "task_name");

        $task = new Task();
        $message = "";

        try {
            $task->updateTask($args, $updates, $conditions);
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

    public function sendConfirmation() {
        $data = json_decode(file_get_contents('php://input'));

        $projectId = $_SESSION['project_id'];
        $taskArgs = array(
            "project_id" => $projectId,
            "task_name" => $data->task_name
        );
        $task = new Task();
        $taskData = $task->getTask($taskArgs, array("project_id", "task_name"));
        // $taskData = $task->getTask($taskArgs);

        if($taskData){
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
        
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => "OK!"
            ]
        );
        
    }

    public function goToGroup(array $data){
        
        try {
            $payload = $this->userAuth->getCredentials(); // get the payload content
            $project = new Project($payload->id);
            $group = new Group();

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

                        $this->sendResponse(
                            view: "/group_leader/dashboard.html",
                            status: "success",
                            content: $groupController->getGroupTasks($args, $payload->id)
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

    public function getForum(){
        $this->sendResponse(
            view: "/project_member/forum.html",
            status: "success"
            // content: $project->readProjectsOfUser($user_id, $project_id) ? $data : array()
        );
    }

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

        $user = new User();
        $data = array("groups" => $groups, "projectData" => $project->getProject(array("id" => $project_id)));

        // get project members' details
        $projectMemberCondition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id AND role = :role)";
        $groupLeaderCondition = "WHERE id IN (SELECT DISTINCT leader_id FROM groups WHERE project_id = :project_id)";

        $data['projectLeader'] = $user->getAllUsers(array("project_id" => $project_id, "role" => "LEADER"), $projectMemberCondition);
        $data['projectMembers'] = $user->getAllUsers(array("project_id" => $project_id, "role" => "MEMBER"), $projectMemberCondition);
        $data['groupLeaders'] = $user->getAllUsers(array("project_id" => $project_id), $groupLeaderCondition);

        $this->sendResponse(
            view: "/project_member/getProjectInfo.html",
            status: "success",
            content: $project->readProjectsOfUser($user_id, $project_id) ? $data : array()
        );
    }
    
}
