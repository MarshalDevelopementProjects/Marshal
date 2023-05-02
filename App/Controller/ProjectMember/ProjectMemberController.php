<?php

namespace App\Controller\ProjectMember;

use App\Controller\User\UserController;
use App\Controller\Group\GroupController;
use App\Controller\Message\MessageController;
use App\Controller\Notification\NotificationController;
use App\Model\Forum;
use App\Model\ProjectMember;
use App\Model\Notification;
use App\Model\Task;
use App\Model\Project;
use App\Model\Group;
use App\Model\User;
use App\Model\File;
use App\Model\Message;
use App\Model\Forum;
use Core\Validator\Validator;
use Core\FileUploader;
use Exception;
use Throwable;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectMemberController extends UserController
{
    protected Project $project;

    protected Forum $forum;
    private ProjectMember $projectMember;
    protected Project $project;
    protected Forum $forum;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("project_id", $_SESSION)) {
                $this->project = new Project(member_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"]);
                $this->projectMember = new ProjectMember($_SESSION["project_id"]);
                $this->forum = new Forum();
            } else {
                throw new Exception("Bad request missing arguments");
            }
        } catch (Exception $exception) {
            throw $exception;
        }
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
            "member_id" => $user_id,
            "project_id" => $project_id,
            "task_name" => $data->task_name
        );
        $updates = array("status", "member_id");
        $conditions = array("project_id", "task_name");

        $message = "";

        try {
            $task->updateTask($args, $updates, $conditions);
            $message = "Successfully picked";

            // send notification to leader

            $notificationController = new NotificationController();
            $thisProject = $project->getProject(array('id' => $project_id));

            $args = array(
                "message" => "I pickup " . $data->task_name . ".",
                "type" => "notification",
                "sender_id" => $user_id,
                "url" => "http://localhost/public/user/project?id=" . $project_id,
                "recive_id" => $thisProject->created_by
            );

            $notificationId = $notificationController->setNotification($args);

            // set task refference 
            $pickupedTask = $task->getTask(array("project_id" => $project_id, "task_name" => $data->task_name), array("project_id", "task_name"));
            $notification->addTaskRefference(array("notification_id" => $notificationId, "task_id" => $pickupedTask->task_id), array("notification_id", "task_id"));
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
        // var_dump($data);

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
            "task_id" => $taskId,
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

            $notificationController = new NotificationController();
            $thisProject = $project->getProject(array('id' => $projectId));

            $args = array(
                "message" => $data->confirmation_message,
                "type" => "notification",
                "sender_id" => $user_id,
                "url" => "http://localhost/public/user/project?id=" . $projectId,
                "recive_id" => $thisProject->created_by
            );

            $notificationId = $notificationController->setNotification($args);

            // set task refference 
            $completedTask = $task->getTask(array("project_id" => $projectId, "task_name" => $data->task_name), array("project_id", "task_name"));
            $notification->addTaskRefference(array("notification_id" => $notificationId, "task_id" => $completedTask->task_id), array("notification_id", "task_id"));
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
                $role = $group->getGroupMember($args, array("group_id", "member_id"))->role;

                // project leader also has group leader features
                $projectDetails = $project->getProject(array("id" => $_SESSION['project_id']));
                if ($projectDetails->created_by == $payload->id) {
                    $role = 'LEADER';
                }
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

                switch ($role) {
                    case 'LEADER':

                        $this->sendResponse(
                            view: "/group_leader/dashboard.html",
                            status: "success",
                            content: $groupData
                        );
                        break;

                    case 'MEMBER':

                        $this->sendResponse(
                            view: "/group_member/dashboard.html",
                            status: "success",
                            content: $groupData
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
    public function getForum(): void
    {
        $this->sendResponse(
            view: "/project_member/forum.html",
            status: "success",
            content: array(
                "project_id" => $_SESSION["project_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
                "messages" => $this->forum->getForumMessages(project_id: $_SESSION["project_id"]) ? $this->forum->getMessageData() : [],
                "members" =>  $this->projectMember->getProjectMembers() ? $this->projectMember->getProjectMemberData() : [],
            ) + parent::getTaskDeadlines()
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
        if ($groups) {
            foreach ($groups as $groupData) {
                if ($group->getGroupMember(array("group_id" => $groupData->id, "member_id" => $user_id), array("group_id", "member_id"))) {
                    $groupData->hasAccess = true;
                } else {
                    $groupData->hasAccess = false;
                }
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

<<<<<<< HEAD
        $data["progress"] = $this->project->getProjectProgress(project_id: $_SESSION["project_id"]);
=======
        $data["progress"] = $project->getProjectProgress(project_id: $_SESSION["project_id"]);
>>>>>>> main

        $this->sendResponse(
            view: "/project_member/getProjectInfo.html",
            status: "success",
            content: $project->readProjectsOfUser($user_id, $project_id) ? $data : array()
        );
    }

    // file uploader
    public function getFileUploadPage()
    {
        $fileModel = new File();
        $user = new User();

        $condition = "project_id = " . $_SESSION['project_id'];
        $files = $fileModel->getFiles($condition);

        foreach ($files as $file) {
            $uploadedUser = $user->readMember("id", $file->uploader_id);
            $file->uploaderName = $uploadedUser->first_name . " " . $uploadedUser->last_name;
            $file->profile = $uploadedUser->profile_picture;
        }
        $data = array(
            "files" => $files,
            "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
        );
        $data += parent::getTaskDeadlines();

        $this->sendResponse(
            view: "/project_member/file_uploader.html",
            status: "success",
            content: $data
        );
    }

    private function getFileType($extension)
    {

        if ($extension == "txt") {
            return "text";
        } else if ($extension == "docx" || $extension == "doc" || $extension == "odt") {
            return "document";
        } else if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
            return "spreadsheet";
        } else if ($extension == "pptx" || $extension == "ppt" || $extension == "odp") {
            return "presentation";
        } else if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
            return "image";
        } else if ($extension == "mp3" || $extension == "wav") {
            return "audio";
        } else if ($extension == "mp4" || $extension == "mkv") {
            return "video";
        } else if ($extension == "pdf") {
            return "pdf";
        } else {
            return "unknown";
        }
    }
    public function fileUpload()
    {

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $payload = $this->userAuth->getCredentials();

        $pathinfo = pathinfo($_FILES['uploadedfile']["name"]);
        $base = $pathinfo["filename"];
        $base = preg_replace("/[^\w-]/", "_", $base);
        $filename = $base . "." . $pathinfo["extension"];

        $type = $this->getFileType($pathinfo["extension"]);
        $sql = "INSERT INTO `files` (`fileName`, `fileType`, `project_id`, `uploader_id`, `filePath`) VALUES ('" . $filename . "', '" . $type . "', " . $_SESSION['project_id'] . ", " . $payload->id . ", :uploadedfile)";
        // var_dump($sql);
        $result = FileUploader::upload(
            allowed_file_types: array("image/jpg", "image/png", "image/gif", "image/jpeg", "document/pdf"),
            fields: array(
                "uploadedfile" => array(
                    "upload_to" => "/App/Database/Uploads/Files",
                    "upload_as" => "",
                    "query" => $sql,
                    "max_cap" => 102400000 // file size in binary bytes
                )
            )
        );

        // $data = json_decode(file_get_contents('php://input'));
        // var_dump($data);

        if ($result) {
            $this->sendJsonResponse(
                status: "success",
                content: [
                    "message" => "Profile picture successfully updated"
                ]
            );
        } else {
            $this->sendResponse(
                view: "/errors/500.html",
                status: "error",
                content: [
                    "message" => "Image cannot be uploaded"
                ]
            );
        }
    }

    // the following functions sends JSON responses

    // save the message to the project table
    // $args format {"message" => "message string"}
    public function postMessageToProjectForum(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // get the user id
        if (!empty($args) && array_key_exists("message", $args)) {
            if (!empty($args["message"])) {
                try {
                    if ($this->forum->saveForumMessage(sender_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], msg: $args["message"])) {
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
    public function getProjectForumMessages(): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if ($this->forum->getForumMessages(project_id: $_SESSION["project_id"])) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->forum->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // save the message to the task table
    // $args format {"task_id" => "TaskID", "message" => "message string"}
    public function postMessageToProjectTaskFeedback(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if (!empty($args) && array_key_exists("message", $args) && array_key_exists("task_id", $args)) {
                if (!empty($args["message"])) {
                    if ($this->forum->saveProjectTaskFeedbackMessage(sender_id: $this->user->getUserData()->id, project_id: $_SESSION["project_id"], task_id: $args["task_id"], msg: $args["message"])) {
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
    public function getProjectTaskFeedbackMessages(array $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (array_key_exists("task_id", $args)) {
                if ($this->forum->getProjectTaskFeedbackMessages(project_id: $_SESSION["project_id"], task_id: $args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->forum->getMessageData() ?? []]);
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
        if ($payload->id == $thisProject->created_by) {
            $reciverId = $thisTask->member_id;
        }
        // send notification to reciever

        if ($thisTask->member_id != $thisProject->created_by) {
            try {
                $notification = new Notification();
                $notificationController = new NotificationController();

                $date = date("Y-m-d H:i:s");

                $args = array(
                    "message" => $data->feedbackMessage,
                    "type" => "notification",
                    "sender_id" => $payload->id,
                    "url" =>  "http://localhost/public/user/project?id=" . $_SESSION['project_id'],
                    "recive_id" => $reciverId
                );

                $notificationId = $notificationController->setNotification($args);
                $notification->addTaskRefference(array("notification_id" => $notificationId, "task_id" => $data->task_id), array("notification_id", "task_id"));
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
            ]
        );
    }

    public function getTaskFeedback()
    {

        try {
            $task_id = $_GET['task'];
            $messageController = new MessageController();

            $condition = "id IN(SELECT message_id FROM `project_task_feedback_message` WHERE task_id =" . $task_id . " AND project_id = " . $_SESSION['project_id'] . ") ORDER BY `stamp` LIMIT 100";
            $feedbackMessages = $messageController->receive($condition);

            foreach ($feedbackMessages as $feedback) {
                if ($feedback->sender_id != $this->user->getUserData()->id) {
                    $user = new User();
                    $user->readUser("id", $feedback->sender_id);

                    $sender = $user->getUserData();
                    $feedback->profile = $sender->profile_picture;
                    $feedback->type = "incoming";
                } else {
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

    /**
     * ###Function description###
     * Redirects a project member to the meeting/conference page for video chatting
     * Function returns nothing and accept no arguments
     * #Currently not enabled#
     */
    /*public function gotoConference(): void
    {
        $this->sendResponse(
            view: "/user/meeting.html",
            status: "success",
            content: []
        );
    }*/

    public function getProjectAnnouncements()
    {
        $messageController = new MessageController();
        $message = new Message();
        $user = new User();

        $condition = "id IN(SELECT message_id FROM `project_announcement` WHERE project_id = " . $_SESSION['project_id'] . ") ORDER BY `stamp` LIMIT 100";

        $announcements = $messageController->receive($condition);
        foreach ($announcements as $announcement) {
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
