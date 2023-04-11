<?php

namespace App\Controller\GroupLeader;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\ProjectMember\ProjectMemberController;
use App\Controller\Message\MessageController;
use App\Model\GroupLeader;
use App\Model\User;
use App\Model\Project;
use App\Model\Message;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Group;

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
                        // $notificationArgs = array(
                        //     "projectId" => $_SESSION['project_id'],
                        //     "message" => "You are assigned to " . $args['taskname'] . " by project leader",
                        //     "type" => "notification",
                        //     "senderId" => $user_id,
                        //     "sendTime" => $date
                        // );
                        // $notification = new Notification();
                        // $notification->createNotification($notificationArgs);

                        // $notifyConditions = array(
                        //     "projectId" => $_SESSION['project_id'],
                        //     "senderId" => $user_id,
                        //     "sendTime" => $date
                        // );
                        // $newNotification = $notification->getNotificationData($notifyConditions);
                        // $newNotificationId = $newNotification[0]->id;

                        // $notifyMemberArgs = array(
                        //     "notificationId" => $newNotificationId,
                        //     "memberId" => $receivedUser->id
                        // );
                        // $notification->setNotifiedMembers($notifyMemberArgs);
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
            $successMessage = "Message sent successfully";
        } catch (\Throwable $th) {
            // $successMessage = "Message sent failed";
            throw $th;
        }

        
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
            ]
        );
    }
}
