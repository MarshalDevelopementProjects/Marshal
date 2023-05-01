<?php

namespace App\Controller\ProjectLeader;

use App\Controller\ProjectMember\ProjectMemberController;
use App\Controller\Message\MessageController;
use App\Controller\Notification\NotificationController;
use App\Model\ProjectLeader;
use App\Model\Project;
use App\Model\Notification;
use App\Model\User;
use App\Model\Task;
use App\Model\Group;
use App\Controller\Conference\ConferenceController;
use App\Model\Message;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectLeaderController extends ProjectMemberController
{
    private ProjectLeader $projectLeader;
    private Project $project;
    private ConferenceController $conferenceController;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("project_id", $_SESSION)) {
                $this->projectLeader = new ProjectLeader($_SESSION["project_id"]);
                $this->conferenceController = new ConferenceController();
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
        // TODO: IN THE AUTH CHECK THE ROLE WHETHER THE INCOMING REQUEST OWNER IS ACTUALLY THE PROJECT LEADER OF THIS PROJECT
        return parent::auth();
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
        $projectLeaderCondition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id AND role = :role)";
        $projectMemberCondition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id AND ( role = :role OR role = :role2))";
        $groupLeaderCondition = "WHERE id IN (SELECT DISTINCT leader_id FROM groups WHERE project_id = :project_id)";

        $data['projectLeader'] = $user->getAllUsers(array("project_id" => $project_id, "role" => "LEADER"), $projectLeaderCondition);
        $data['projectMembers'] = $user->getAllUsers(array("project_id" => $project_id, "role" => "MEMBER", "role2" => "LEADER"), $projectMemberCondition);
        $data['groupLeaders'] = $user->getAllUsers(array("project_id" => $project_id), $groupLeaderCondition);

        $data += parent::getTaskDeadlines();
        // get user profile
        $user = new User();

        if ($user->readUser("id", $payload->id)) {
            $data += array("profile" => $user->getUserData()->profile_picture);
        }

        if ($this->projectLeader->getProjectFeedbackMessages()) {
            $data["feedback_messages"] = $this->projectLeader->getMessageData();
        }

        $this->sendResponse(
            view: "/project_leader/getProjectInfo.html",
            status: "success",
            content: $project->readProjectsOfUser($user_id, $project_id) ? $data : array()
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

                $notificationController = new NotificationController();

                $args = array(
                    "message" => "Invite you to the peoject.",
                    "type" => "request",
                    "sender_id" => $user_id,
                    "url" => "http://localhost/public/user/project?id=" . $project_id,
                    "recive_id" => $receivedUser->id
                );
                
                $notificationId = $notificationController->setNotification($args);
            }
           
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

                        $notificationController = new NotificationController();
                        // $thisProject = $project->getProject(array('id' => $project_id));

                        $notificationArgs = array(
                            "message" => "You are assigned to " . $args['taskname'] . " by project leader.",
                            "type" => "notification",
                            "sender_id" => $user_id,
                            "url" => "http://localhost/public/user/project?id=" . $_SESSION['project_id'],
                            "recive_id" => $receivedUser->id
                        );
                        
                        $notificationId = $notificationController->setNotification($notificationArgs);
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
            if ($task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status"))) {

                if($args['assignedMember']){
                    $args = array(
                        "status" => "ONGOING",
                        "member_id" => $receivedUser->id,
                        "project_id" => $_SESSION['project_id'],
                        "task_name" => $args['taskname']
                    );
                    $updates = array("status", "member_id");
                    $conditions = array("project_id", "task_name");
    
                    $task->updateTask($args, $updates, $conditions);
                }
                
                header("Location: http://localhost/public/user/project?id=" . $_SESSION['project_id']);
            } else {
                echo "Fail";
            }
        } else {
            header("Location: http://localhost/public/user/project?id=" . $_SESSION['project_id']);
        }
    }


    public function rearangeTask()
    {
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
        $message = new Message();
        $notification = new Notification();

        $successMessage = "";

        if ($data->new_board === "TO-DO") {
            $args['member_id'] = NULL;
            $updates[] = "member_id";

            // we have to delete all messages as well related to that task
            $rearrangedTask = $task->getTask(array("project_id" => $project_id, "task_name" => $data->task_name), array("project_id", "task_name"));
            $messageCondition = "WHERE id IN (SELECT message_id FROM project_task_feedback_message WHERE task_id = " . $rearrangedTask->task_id . " AND project_id = " . $project_id . ")";

            $message->deleteMessage($messageCondition, "message");

            // we have to delete notifications related to that task messages
            $notificationCondition = "WHERE id IN (SELECT notification_id FROM task_notification WHERE task_id = " . $rearrangedTask->task_id . ")";
            $notification->deleteNotification($notificationCondition, "notifications");
        }
        try {
            $task->updateTask($args, $updates, $conditions);
            $successMessage = "Reported successfully";
        } catch (\Throwable $th) {
            $successMessage = "Failed to rearange the task";
        }
        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $successMessage
            ]
        );
    }

    public function assignTask()
    {
        $data = json_decode(file_get_contents('php://input'));

        $user = new User();
        $notificationController = new NotificationController();

        $user->readUser("username", $data->member_username);
        $receivedUser = $user->getUserData();

        $payload = $this->userAuth->getCredentials();
        $project_id = $_SESSION["project_id"];
        $user_id = $payload->id;

        if ($receivedUser) {
            $args = array(
                "status" => "ONGOING",
                "member_id" => $receivedUser->id,
                "project_id" => $project_id,
                "task_name" => $data->task_name
            );

            $task = new Task();
            $message = "";

            $updates = array("status", "member_id");
            $conditions = array("project_id", "task_name");

            try {
                $task->updateTask($args, $updates, $conditions);
                $message = "Successfully handovered the task.";

                // send notification to Member

                $notificationArgs = array(
                    "message" => "Leader assigned you to " . $data->task_name . '.',
                    "type" => "notification",
                    "sender_id" => $user_id,
                    "url" => "http://localhost/public/user/project?id=" . $project_id,
                    "recive_id" => $receivedUser->id
                );
                
                $notificationId = $notificationController->setNotification($notificationArgs);
                
            } catch (\Throwable $th) {
                $message = "Failed to handover the task: " . $th->getMessage();
            }

            $this->sendJsonResponse(
                status: "success",
                content: [
                    "message" => $message
                ]
            );
        }
    }

    public function createGroup()
    {
        $user = new User();

        $data = $_POST;
        $project_id = $_SESSION['project_id'];

        $payload = $this->userAuth->getCredentials();
        $user_id = $payload->id;

        $leaderId = $user_id;
        if ($data['assignMember']) {

            $assignedMember = $user->readMember("username", $data['assignMember']);
            if($assignedMember){
                $leaderId = $assignedMember->id;
            }
        }

        $args = array(
            "group_name" => $data['group_name'],
            "task_name" => $data['task_name'],
            "description" => $data['group_description'],
            "project_id" => $project_id,
            "leader_id" => $leaderId
        );
        $keys = array("group_name", "task_name", "description", "project_id", "leader_id");

        // set the task as well
        $taskArgs = array(
            "project_id" => $project_id,
            "description" => $data['group_description'],
            "task_name" => $data['task_name'],
            "priority" => "high",
            "status" => "ONGOING",
            "assign_type" => "group",
            "member_id" => $leaderId,
            "deadline" => $data['deadline']
        );

        $group = new Group();
        $task = new Task();

        $message = "";
        try {
            $group->createGroup($args, $keys);
            $task->createTask($taskArgs, array("project_id", "description", "task_name", "priority", "status", "assign_type", "member_id", "deadline"));

            // until project leader add a new group leader, he or she will be the group leader
            $newGroup = $group->getGroup(array("group_name" => $data['group_name'], "project_id" => $project_id,), array("group_name", "project_id"));
            $addMemberArgs = array(
                "group_id" => $newGroup->id,
                "member_id" => $leaderId,
                "role" => "LEADER",
                "joined" => date("Y-m-d H:i:s")
            );

            $group->addGroupMember($addMemberArgs, array("group_id", "member_id", "role", "joined"));

            // if project leader assign a member to lead the group then project leader also become just a group member
            if ($assignedMember){
                $memberArgs = array(
                    "group_id" => $newGroup->id,
                    "member_id" => $user_id,
                    "role" => "MEMBER",
                    "joined" => date("Y-m-d H:i:s")
                );
                $group->addGroupMember($memberArgs, array("group_id", "member_id", "role", "joined"));
            }

            $message = "Successfully created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            // throw $th;
        }

        header("Location: http://localhost/public/projectleader/getinfo");
    }

    public function addAnnouncement()
    {
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
            "message_type" => "PROJECT_ANNOUNCEMENT",
            "msg" => $data->announcementMessage
        );
        try {
            $messageController->send($args, array("sender_id", "stamp", "message_type", "msg"));

            $newMessage = $message->getMessage(array("sender_id" => $payload->id, "stamp" => $date, "message_type" => "PROJECT_ANNOUNCEMENT"), array("sender_id", "stamp", "message_type"));

            $messageTypeArgs = array(
                "message_id" => $newMessage->id,
                "project_id" => $_SESSION['project_id'],
                "heading" => $data->announcementHeading
            );

            $message->setMessageType($messageTypeArgs, array("message_id", "project_id", "heading"), "project_announcement");
            $successMessage = "Message sent successfully";
        } catch (\Throwable $th) {
            $successMessage = "Message sent failed";
            // throw $th;
        }

        try {
            $notificationController = new NotificationController();

            $args = array(
                "message" => $data->announcementHeading,
                "type" => "notification",
                "sender_id" => $payload->id,
                "url" => "http://localhost/public/projectmember/getinfo",
                "recive_id" => null
            );
            
            $notificationId = $notificationController->setNotification($args);
            
            $members = $project->getProjectUsers("WHERE project_id = " . $_SESSION['project_id'] . " AND `role` = 'MEMBER'");
            $notificationController->boardcastNotification($notificationId, $members);

        } catch (\Throwable $th) {
            $successMessage = $th->getMessage();
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
            view: "/project_leader/forum.html",
            status: "success",
            content: [
                "project_id" => $_SESSION["project_id"],
                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
                "messages" => $this->projectLeader->getForumMessages() ? $this->projectLeader->getMessageData() : [],
                "members" =>  $this->projectLeader->getProjectMembers() ? $this->projectLeader->getProjectMemberData() : [],
            ] + parent::getTaskDeadlines()
        );
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
                    if ($this->projectLeader->saveForumMessage(id: $this->user->getUserData()->id, msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("internal_server_error", ["message" => "Message cannot be saved!"]);
                    }
                } catch (\Exception $exception) {
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
            if ($this->projectLeader->getForumMessages()) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectLeader->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // save the message to the project table
    // $args format {"message" => "message string"}
    public function postMessageToProjectFeedback(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    if ($this->projectLeader->saveProjectFeedbackMessage(id: $this->user->getUserData()->id, msg: $args["message"])) {
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

    public function getProjectFeedbackMessages(): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if ($this->projectLeader->getProjectFeedbackMessages()) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectLeader->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (\Exception $exception) {
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
                    if ($this->projectLeader->saveProjectTaskFeedbackMessage(id: $this->user->getUserData()->id, task_id: $args["task_id"], msg: $args["message"])) {
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
                if ($this->projectLeader->getProjectTaskFeedbackMessages($args["task_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectLeader->getMessageData() ?? []]);
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

    // assumes that the project leader is in the group feedback page
    // when these functions are called
    // $args must follow this format
    // $args = ["message" => "message string"];
    public function postMessageToGroupFeedback(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (!empty($args) && array_key_exists("message", $args) && array_key_exists("group_id", $_SESSION)) {
                if (!empty($args["message"])) {
                    if ($this->projectLeader->saveGroupFeedbackMessage(id: $this->user->getUserData()->id, group_id: $_SESSION["group_id"], msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("error", ["message" => "No such group!"]);
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

    // $args must follow this format
    public function getGroupFeedbackMessages(): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        // TODO: HERE THE GROUP NUMBER CAN ONLY BE AN INTEGER REJECT ANY OTHER FORMAT
        // TODO: SO THAT YOU WILL BE ABLE TO RETURN THE GROUP CANNOT BE FOUND ERROR
        try {
            if (array_key_exists("group_id", $_SESSION)) {
                if ($this->projectLeader->getGroupFeedbackMessages($_SESSION["group_id"])) {
                    $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->projectLeader->getMessageData() ?? []]);
                } else {
                    $this->sendJsonResponse("error", ["message" => "Group is not valid"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Invalid input format"]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Redirects a project leader to the meeting/conference page for video chatting
     * Function returns nothing and accept no arguments
     *
     * The input array must be of the following format
     *
     * $args = [
     *      "conf_id" => "ID of the conference the user wants to join",
     * ]
     *
     * @throws Exception
     */
    public function gotoConference(array $args): void
    {
        // TODO: Depending on the conference user want to join redirect him
        $this->sendResponse(
            view: "/user/meeting.html",
            status: "success",
            // TODO: PASS THE NECESSARY INFORMATION OF THE REDIRECTING PAGE
            content: [
                "user_data" => [
                    "username" => $this->user->getUserData()->username,
                    "profile_picture" => $this->user->getUserData()->profile_picture,
                    "peer" => $this->projectLeader->getProjectMembersByRole($_SESSION["project_id"], "CLIENT")
                ]
            ]
        );
    }

    /**
     * ###Function description###
     * Redirects a project leader to the meeting/conference scheduling page to
     * schedule a video conference or to check the conferences
     * Function returns nothing and accept no arguments
     * @throws Exception
     */
    public function gotoConferenceScheduler(): void
    {
        $this->sendResponse(
            view: "/project_leader/conference_scheduler.html",
            status: "success",
            // TODO: PASS THE NECESSARY INFORMATION OF THE REDIRECTING PAGE
            content: [
                "user_data" => [
                    "username" => $this->user->getUserData()->username,
                    "profile_picture" => $this->user->getUserData()->profile_picture
                ],
                "project_conference_details" => $this->conferenceController->getScheduledConferenceDetailsByProject(
                    id: $this->user->getUserData()->id,
                    project_id: $_SESSION["project_id"],
                    initiator: "LEADER"
                ),
                "all_conference_details" => $this->conferenceController->getScheduledConferenceDetails(
                    id: $this->user->getUserData()->id,
                    initiator: "LEADER"
                ),
                "clients_of_the_project" => $this->projectLeader->getProjectMembersByRole(
                    project_id: $_SESSION["project_id"],
                    role: "CLIENT"
                ) ? $this->projectLeader->getProjectData() : [],
                "message" => "Successfully retrieved"
            ]
        );
    }

    /**
     * ###Function description###
     * Schedule a conference using the valid information provided by the
     * project leader, if invalid information was provided the user will be
     * informed
     *
     * The input array must be of the following format
     *
     * $args = [
     *      "conf_name" => "name of the conference scheduled by the user",
     *      "on" => "the date on which the conference will be held",
     *      "at" => "at what time will be conference will be held",
     * ]
     *
     * user ID will be added later here (leader_id)
     * and since this is the project leader ultimately for a given project the client will be
     * added to the args in this function as well (client_id)
     * get the project_id from the session
     *
     * TODO: STILL THE SAME TIME AND DATE PROBLEM EXISTS
     *
     */
    public function scheduleConference(array|object $args): void
    {
        try {
            $args["leader_id"] = $this->user->getUserData()->id;
            if ($this->projectLeader->getProjectMembersByRole(project_id: $_SESSION["project_id"], role: "CLIENT")) {
                if (!empty($this->projectLeader->getProjectData())) {
                    $args["client_id"] = $this->projectLeader->getProjectData()[0]->id;
                    $returned = $this->conferenceController->scheduleConference(args: $args);
                    if (is_bool($returned) && $returned) {
                        $this->sendJsonResponse(status: "success", content: [
                            "message" => "Meeting was successfully added to the schedule",
                        ]);
                    } else {
                        $this->sendJsonResponse(status: "error", content: [
                            "message" => "Meeting cannot be successfully scheduled",
                            "errors" => $returned
                        ]);
                    }
                } else {
                    $this->sendJsonResponse(status: "error", content: [
                        "message" => "You don't have any client in this project, cannot schedule meetings yet!",
                    ]);
                }
            } else {
                $this->sendJsonResponse(status: "error", content: [
                    "message" => "You don't have any client in this project, cannot schedule meetings yet!",
                ]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    /**
     * ####Function description####
     * Used to change the status code of a given Schedule
     * Check the model of the conference controller to get an IDEA<br>
     * ANY CONFERENCE WITH THE STATUS CODE "CANCELLED" CANNOT BE ALTERED
     *
     * The format of the args should be  => [
     *                                          "conf_id" => "id of the conference that we are going to change the status of",
     *                                          "status"  => "The new status"
     *                                      ]
     *
     *
     * */
    public function setStatusOfSchedule(array $args): void
    {
        try {
            $returned = $this->conferenceController->changeConferenceStatus($args);
            if (is_bool($returned) && $returned) {
                $this->sendJsonResponse("success", ["message" => "Status is successfully changed"]);
            } else {
                $this->sendJsonResponse("error", ["message"  => "Status cannot be changed", "errors" => $returned]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Find conference details of a particular project leader(user id is taken from the JWT payload)
     * (all the conference scheduled will be returned to the user regardless the project)
     */
    public function getScheduledConferenceDetails(): void
    {
        try {
            $returned = $this->conferenceController->getScheduledConferenceDetails(
                id: $this->user->getUserData()->id,
                initiator: "LEADER"
            );
            $this->sendJsonResponse("success", ["message" => "Data retrieved", "conferences" => $returned]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getScheduledConferenceDetailsOfProject(): void
    {
        try {
            $returned = $this->conferenceController->getScheduledConferenceDetailsByProject(
                id: $this->user->getUserData()->id,
                project_id: $_SESSION["project_id"],
                initiator: "LEADER"
            );
            $this->sendJsonResponse("success", ["message" => "Data retrieved", "conferences" => $returned]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
