<?php

namespace App\Controller\GroupLeader;

use App\Controller\GroupMember\GroupMemberController;
use App\Controller\Notification\NotificationController;
use App\Controller\Message\MessageController;
use App\Controller\PDF\PDFController;
use App\Model\GroupLeader;
use App\Model\User;
use App\Model\Project;
use App\Model\Message;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Group;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class GroupLeaderController extends GroupMemberController
{
    private GroupLeader $groupLeader;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("group_id", $_SESSION) && $this->user->checkUserRole(req_id: $_SESSION["group_id"], role: "LEADER", type: "GROUP")) {
                $this->groupLeader = new GroupLeader($_SESSION["project_id"], $_SESSION["group_id"]);
            } else {
                $this->sendResponse(
                    view: "/errors/403.html",
                    status: "unauthorized"
                );
                // throw new Exception("Bad request missing arguments");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createTask($args)
    {
        // var_dump($args);
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

    /**
     * @throws Exception
     */
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
        if ($user->readUser("id", $payload->id)) {
            $userData = $user->getUserData();
        }
        $groupData['userDetails'] = $userData->profile_picture;
        $groupData['projectDetails'] = $project->getProject(array("id" => $_SESSION['project_id']))->project_name;

        $groupData += parent::getTaskDeadlines();

        if ($this->forum->getGroupFeedbackForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"])) {
            $groupData["feedback_messages"] = $this->forum->getMessageData();
        }

        $groupData["user_data"] = [
            "username" => $this->user->getUserData()->username,
            "profile_picture" => $this->user->getUserData()->profile_picture,
        ];

        $groupData["progress"] = $group->getGroupProgress(group_id: $_SESSION["group_id"]);

        if ($group->getGroupMembers_(group_id: $_SESSION["group_id"])) {
            $groupData["members"] = $group->getGroupMemberData();
        }

        if($group->getGroupStatistics(group_id: $_SESSION["group_id"])) {
            $groupData["stat"] = $group->getGroupData();
        }

        $this->sendResponse(
            view: "/group_leader/groupInfo.html",
            status: "success",
            content: $groupData
        );
    }

    public function addAnnouncement()
    {
        $data = json_decode(file_get_contents('php://input'));

        $successMessage = "";
        $payload = $this->userAuth->getCredentials();
        $messageController = new MessageController();
        $message = new Message();
        $user = new User();
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
                "group_id" => $_SESSION['group_id'],
                "heading" => $data->announcementHeading
            );

            $message->setMessageType($messageTypeArgs, array("message_id", "project_id", "group_id", "heading"), "group_announcement");

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
                
                // $condition = "WHERE id IN (SELECT member_id FROM group_join WHERE group_id = :group_id)";
                // $members = $user->getAllUsers(array("group_id" => $_SESSION['group_id']), $condition);
                $members = $group->getGroupMembers(array("group_id" => $_SESSION['group_id']), array("group_id"));
                
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

    /**
     * @throws Exception
     */
    public function getForum(): void
    {
        $this->sendResponse(
            view: "/group_leader/forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "group_id" => $_SESSION["group_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture],
                "messages" => $this->forum->getGroupForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"]) ? $this->forum->getMessageData() : [],
                "members" =>  $this->groupLeader->getGroupMembers() ? $this->groupLeader->getGroupMemberData() : [],
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function getGroupFeedbackForum(): void
    {
        $this->sendResponse(
            view: "/group_leader/feedback_forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "group_id" => $_SESSION["group_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture],
                "messages" => $this->forum->getGroupFeedbackForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"]) ? $this->forum->getMessageData() : [],
            ]
        );
    }

    // $args = ["message" => "message string"];
    public function postMessageToGroupFeedback(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->forum->saveGroupFeedbackMessage(sender_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"], msg: $args["message"])) {
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

    public function getGroupFeedbackMessages(): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if ($this->forum->getGroupFeedbackForumMessages(project_id: $_SESSION["project_id"], group_id: $_SESSION["group_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->forum->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function sendGroupInvitation()
    {
        try {
            // get receiver user name
            $data = file_get_contents('php://input');

            // first check receiver is valid user or not
            // get received user id
            $user = new User();
            $group = new Group();

            $user->readUser("username", $data);
            $receivedUser = $user->getUserData();

            if ($receivedUser) {
                $payload = $this->userAuth->getCredentials();
                $project_id = $_SESSION["project_id"];
                $user_id = $payload->id;

                $groupDetails = $group->getGroup(array("id" => $_SESSION['group_id']), array("id"));

                $memberArgs = array(
                    "group_id" => $_SESSION['group_id'],
                    "member_id" => $receivedUser->id,
                    "role" => "MEMBER",
                    "joined" => date("Y-m-d H:i:s")
                );
                $group->addGroupMember($memberArgs, array("group_id", "member_id", "role", "joined"));

                $notificationController = new NotificationController();

                $args = array(
                    "message" => "You are a member of the group " . $groupDetails->group_name . ".",
                    "type" => "request",
                    "sender_id" => $user_id,
                    "url" => "http://localhost/public/projectmember/group?id=" . $_SESSION['group_id'],
                    "recive_id" => $receivedUser->id
                );
                
                $notificationId = $notificationController->setNotification($args);
            }
           
            echo (json_encode(array("message" => "Success")));
        } catch (\Throwable $th) {
            echo (json_encode(array("message" => $th)));
        }
    }

    public function finishGroupTask(){
        $success_message = "";

        $group = new Group();
        $group_data = $group->getGroup(array("id" => $_SESSION['group_id']), array("id"));
        $group_leader_id = $group_data->leader_id;

        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;
        $project = new Project($user_id);

        $notification = new Notification();
        $notificationController = new NotificationController();
        
        $project_data = $project->readProjectData($_SESSION['project_id']) ? $project->getProjectData() : array();

        if($project_data){
            $project_data = $project_data[0];
            if($project_data->created_by == $user_id){

                try {
                    $task = new Task();
                    $task->updateTask(array("task_name" => $group_data->task_name, "assign_type" => 'group', "status" => "DONE"), array("status"), array("task_name", "assign_type"));
                
                    $group->updateGroup(array("id" => $_SESSION['group_id'], "finished" => 1), array("finished"), array("id"));

                    $notification_args = array(
                        "message" => "We are successfully done our task " . $group_data->task_name ." .Thank you for your support.",
                        "type" => "notification",
                        "sender_id" => $group_leader_id,
                        "url" => "http://localhost/public/user/project?id=" . $_SESSION['project_id'],
                        "recive_id" => null
                    );
                    
                    $notificationId = $notificationController->setNotification($notification_args);
                    $members = $group->getGroupMembers(array("group_id" => $_SESSION['group_id']), array("group_id"));
                    
                    $notificationController->boardcastNotification($notificationId, $members);

                    $success_message = "Successfully finish the " . $group_data->task_name . ".";
                } catch (\Throwable $th) {
                    throw $th;
                }
            }else{
                $notification_args = array(
                    "message" => "We almostr done the task " . $group_data->task_name . ".",
                    "type" => "notification",
                    "sender_id" => $group_leader_id,
                    "url" => "http://localhost/public/groupleader/group?id=" . $_SESSION['group_id'],
                    "recive_id" => $project_data->created_by
                );
                
                $notificationId = $notificationController->setNotification($notification_args);
                $success_message = "Successfully notify the project leader to finish this task.";
            }
        }
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $success_message
            ]
        );
    }

    public function generateProjectReport(): void
    {
        try {
            $pdfGenerator = new PDFController();
            // TODO: GET THE PROJECT DATA HERE
            if ($this->group->getPDFData(group_id: $_SESSION["group_id"])) {
                $data = $this->group->getGroupData();
                $pdfGenerator->generateGeneralFormatPDF(
                    path_to_html_markup: "/View/src/group_leader/pdf-templates/pdf-template.html",
                    path_to_style_sheet: "/View/src/group_leader/pdf-templates/pdf-styles.css",
                    file_name: "Report.pdf",
                    attributes: $data,
                    flag: false
                );
            } else {
                $this->sendResponse(
                    view: "/error/505.html",
                    status: "error",
                    content: [
                        "message" => "Pdf file cannot be generated, Sorry for the inconvenience"
                    ]
                );
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
