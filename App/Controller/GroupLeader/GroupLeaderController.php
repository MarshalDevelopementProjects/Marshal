<?php

namespace App\Controller\GroupLeader;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Model\GroupLeader;
use App\Model\User;
use App\Model\Notification;
use App\Model\Task;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupLeaderController extends ProjectMemberController
{
    private GroupLeader $groupLeader;

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
    }

    public function auth()
    {
        return parent::auth();
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
                "status" => $status,
                "task_type" => "group"
            );

            $task = new Task();
            if($task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status", "task_type"))){

                $newTask = $task->getTask(array("project_id" => $_SESSION['project_id'], "task_name" => $args['taskname']), array("project_id", "task_name"));
                $task->addGroupToTask(array("task_id" => $newTask->task_id, "group_id" => $_SESSION['group_id']));
                
                header("Location: http://localhost/public/projectmember/group?id=".$_SESSION['group_id']);
            }else{
                echo "Fail";
            }
        }else{
            header("Location: http://localhost/public/projectmember/group?id=".$_SESSION['group_id']);
        }
    }

    public function getGroupInfo(){
        $this->sendResponse(
            view: "/group_leader/groupInfo.html",
            status: "success",
        );
    }
}
