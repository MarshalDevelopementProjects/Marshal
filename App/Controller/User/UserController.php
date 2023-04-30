<?php

namespace App\Controller\User;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;
use App\Model\User;
use App\Model\Task;
use App\Controller\Controller;
use App\Controller\Project\ProjectController;
use App\Model\Project;
use App\Model\Notification;
use Core\Validator\Validator;
use Core\FileUploader;
use Core\PdfGenerator;
use Exception;

class UserController extends Controller
{
    protected UserAuthController $userAuth;
    protected User $user;
    protected Validator $validator;

    public function __construct()
    {
        try {
            parent::__construct();
            $this->userAuth = new UserAuthController();
            if ($this->auth()) {
                if ($this->userAuth->getCredentials()->primary_role == "admin") {
                    $this->sendResponse(
                        view: "/errors/403.html",
                        status: "unauthorized"
                    );
                } else {
                    $credentials = $this->userAuth->getCredentials();
                    $this->user = new User($credentials->id);
                }
            } else {
                $this->sendResponse(
                    view: "/user/login.html",
                    status: "unauthorized"
                );
            }
            $this->validator = new Validator();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function auth(): bool
    {
        return $this->userAuth->isLogged();
    }

    /**
     * @throws Exception
     */
    public function defaultAction(Object|array|string|int $optional = null): void
    {
        $data = array();
        // get relevant projects
        $payload = $this->userAuth->getCredentials(); // get the payload content
        $project = new Project($payload->id);
        $task = new Task();
        $user = new User();

        $Projects = array();
        if ($project->readProjectsOfUser($payload->id)) {
            $Projects = $project->getProjectData();

            foreach($Projects as $projectData) {
                // get ongoing tasks
                $ongoingTasks = [];
                $tasks = $task->getTasks(array("project_id" => $projectData->id, "status" => "ONGOING"), array("project_id", "status"));
                
                if($tasks){
                    foreach($tasks as $taskData) {
                        
                        array_push($ongoingTasks, $taskData);
                    }
                }
                if($ongoingTasks){
                    $projectData->tasks = $ongoingTasks;
                }else{
                    $projectData->tasks = array();
                }
                
                // get project member profiles
                $condition = "WHERE id IN (SELECT member_id FROM project_join WHERE project_id = :project_id AND ( role = :role OR role = :role2))";
                $projectData->memberProfiles = $user->getUserProfiles(array("project_id" => $projectData->id, "role" => "MEMBER", "role2" => "LEADER"), $condition);

            }
              
        }
        $data += array("projects" => $Projects);

        $userData = array();
        if($user->readUser("id", $payload->id)){
            $userData = $user->getUserData();
        }
        $profile = $userData->profile_picture;
        $data += array("profile" => $profile);
        $data += $this->getTaskDeadlines();

        $this->sendResponse(
            view: "/user/dashboard.html",
            status: "success",
            content: $data
        );
    }

    public function createProject(array $args = array()): void
    {
        $payload = $this->userAuth->getCredentials(); // get the payload content

        // add owner_id
        $args["created_by"] = $payload->id;

        // have to validate user inputs here

        // add the data to the database
        try {
            $project = new Project($payload->id);
            $project->createProject($args);
            $results = $project->getProjectData();

            foreach ($results as $result) {
                unset($result->created_by); // remove the project id from the data sent back need the project id
            }
            // $this->sendJsonResponse("success", array("message" => "Project successfully created", "projects" => $results));
            // $this->sendResponse(
            //     view: "/user/dashboard.html",
            //     status: "success"
            //     // content: array("message" => "User cannot access this project")
            // );
            $this->defaultAction();
            
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function viewProjects(): void
    {
        try {
            $payload = $this->userAuth->getCredentials(); // get the payload content
            $project = new Project($payload->id);
            if ($project->readProjectsOfUser($payload->id)) {
                $this->sendJsonResponse(
                    "success",
                    array(
                        "message" => "user projects",
                        "projects" => $project->getProjectData() // this is an array of objects
                    )
                );
            } else {
                // if there are no projects then an empty string is sent as the message
                $this->sendJsonResponse("success");
            }
        } catch (Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    public function goToProject(array $data): void
    {
        try {
            $payload = $this->userAuth->getCredentials(); // get the payload content
            $project = new Project($payload->id);

            if ($project->readUserRole(member_id: $payload->id, project_id: $data["id"])) {
                // check the user role in the project and redirect him/her to the correct project page
                $_SESSION["project_id"] = $data["id"];
                switch ($project->getProjectData()[0]->role) {
                    case 'LEADER':
                        $args = array(
                            "project_id" => $_SESSION['project_id'],
                            "task_type" => "project"
                        );
                        $projectController = new ProjectController();
                        
                        $data = array();
                        $data['tasks'] = $projectController->getProjectTasks($args, $payload->id);

                        // get project details as well
                        $projectData = array();
                        if($project->readProjectData($_SESSION['project_id'])){
                            $projectData = $project->getProjectData();
                        }
                        $data['projectName'] = $projectData[0]->project_name;

                        // get user profile
                        $user = new User();

                        if($user->readUser("id", $payload->id)){
                            $data += array("profile" => $user->getUserData()->profile_picture);
                        }
                        $data += $this->getTaskDeadlines();

                        $this->sendResponse(
                            view: "/project_leader/dashboard.html",
                            status: "success",
                            content: $data
                        );
                        break;
                    case 'CLIENT':
                        $this->sendResponse(
                            view: "/client/dashboard.html",
                            status: "success",
                            content: [
                                "project_id" => $_SESSION["project_id"],
                                "user_data" => ["username" => $this->user->getUserData()->username, "profile_picture" => $this->user->getUserData()->profile_picture,],
                                "project_details" => $project->readProjectsOfUser(member_id: $payload->id, project_id: $data["id"]) ? $project->getProjectData() : [],
                                "members" => $project->getProjectMembers($_SESSION["project_id"]) ? $project->getProjectMemberData() : []
                            ],
                        );
                        break;
                    case 'MEMBER':

                        $data = array();
                        $args = array(
                            "project_id" => $_SESSION['project_id'],
                            "task_type" => "project"
                        );
                        $projectController = new ProjectController();
                        
                        $data['tasks'] = $projectController->getProjectTasks($args, $payload->id);
                        $data += $this->getTaskDeadlines();

                         // get project details as well
                         $projectData = array();
                         if($project->readProjectData($_SESSION['project_id'])){
                             $projectData = $project->getProjectData();
                         }
                         $data['projectName'] = $projectData[0]->project_name;
 

                        $this->sendResponse(
                            view: "/project_member/dashboard.html",
                            status: "success",
                            content: $data
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
        } catch (Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified", "exceptions" => "$exception"));
        }
    }

    public function viewProfile(): void
    {
        try {
            $user_data = $this->user->getUserData();
            // send the statistical data as well next time
            $this->sendResponse(
                view: "/user/profilepage.html",
                status: "success",
                content: array(
                    "message" => "Successful",
                    "user_info" => array(
                        "username" => $user_data->username,
                        "first_name" => $user_data->first_name,
                        "last_name" => $user_data->last_name,
                        "email_address" => $user_data->email_address,
                        "phone_number" => $user_data->phone_number,
                        "user_status" => $user_data->user_status,
                        "position" => $user_data->position,
                        "display_picture" => $user_data->profile_picture,
                        "bio" => $user_data->bio,
                        "commits"=> $this->user->getCommit($user_data->id)
                    ),
                    "other_info" => array()
                )
            );
        } catch (Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    public function uploadProfilePicture(): void
    {
        // perform additional checks and other validations before giving data to this function
        // and also make sure to construct an appropriate file name for storing the file
        $result = FileUploader::upload(
            allowed_file_types: array("image/jpg", "image/png", "image/gif"),
            fields: array(
                "profile_picture" => array(
                    "upload_to" => "/App/Database/Uploads/ProfilePictures",
                    "upload_as" => "",
                    "query" => "UPDATE `user` SET `profile_picture` = :profile_picture WHERE `id` = {$this->userAuth->getCredentials()->id}",
                    "max_cap" => 6291456 // file size in binary bytes
                )
            )
        );

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

    public function editProfile(array $args = array()): void
    {
        try {
            $old_user_info = $this->user->getUserData();
            $new_user_info = array();
            foreach ($args as $key => $value) {
                if ($old_user_info->$key !== $value) {
                    $new_user_info[$key] = $value;
                } else {
                    $args[$key] = $value;
                }
            }
            if (!empty($new_user_info)) {
                $this->validator->validate($new_user_info, "user_edit_profile");
                if ($this->validator->getPassed()) {
                    $args["id"] = $old_user_info->id;
                    if ($this->user->updateUser(id: $old_user_info->id, args: array_merge($args, $new_user_info))) {
                        $this->user->readUser(key: "id", value: $old_user_info->id);
                        $user_data = $this->user->getUserData();
                        $this->sendJsonResponse(
                            status: "success",
                            content: [
                                "message" => "Profile successfully updated",
                                "user_info" => [
                                    "username" => $user_data->username,
                                    "first_name" => $user_data->first_name,
                                    "last_name" => $user_data->last_name,
                                    "email_address" => $user_data->email_address,
                                    "phone_number" => $user_data->phone_number,
                                    "user_status" => $user_data->user_status,
                                    "position" => $user_data->position,
                                    "display_picture" => $user_data->profile_picture,
                                    "bio" => $user_data->bio
                                ],
                                "other_info" => []
                            ]
                        );
                    } else {
                        $this->sendJsonResponse(
                            status: "error",
                            content: array_merge(
                                [
                                    "message" => "User data cannot be updated",
                                ]
                            )
                        );
                    }
                } else {
                    $this->sendJsonResponse(
                        status: "error",
                        content: array_merge(
                            [
                                "message" => "Please check your inputs",
                                "errors" => $this->validator->getErrors()
                            ]
                        )
                    );
                }
            } else {
                $this->sendJsonResponse(
                    status: "success",
                    content: array_merge([
                        "message" => "Nothing new to update"
                    ])
                );
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getUserData(): array|object|null
    {
        return $this->user->getUserData();
    }


    public function userJoinOnProject(): void
    {
        $projectId = $_GET['data1'];
        $notificationId = $_GET['data2'];

        $payload = $this->userAuth->getCredentials();
        $userId = $payload->id;

        $args = array(
            "project_id" => $projectId,
            "member_id" => $userId,
            "role" => "MEMBER",
            "joined" => date("Y-m-d H:i:s")
        );

        $project = new Project($userId);

        // set as read the notification 

        if ($project->joinProject($args) && $this->readNotification(array("notification_id" => $notificationId, "member_id" => $userId))) {
            $this->sendResponse(
                view: "/user/login.html",
                status: "success"
            );
        } else {
            $this->sendResponse(
                view: "/user/signup.html",
                status: "success"
            );
        }
        // we should send the notification to leader to inform our response
        $this->sendResponseNotification($notificationId, $projectId);
    }

    public function clickOnNotification(){
        $notification_id = $_GET['data'];
        $payload = $this->userAuth->getCredentials();

        // set the notification as read 
        $notification = new Notification();
        $notification->readNotification(array("notification_id" => $notification_id, "member_id" => $payload->id));

        // redirect to notification URL
        $clickedNotification = $notification->getNotification(array("id" => $notification_id), array("id"));
        // var_dump($clickedNotification);
        header("Location: " . $clickedNotification->url);
        exit();
    }

    public function sketch()
    {
        $this->sendResponse(
            view: "/user/sketch.html",
            status: "success",
        );
    }

    // change the user password
    //
    // for the post request, need the following format 
    // ["old_password" => "old password of the user"]
    // 
    // for the put request, need the following format
    // ["new_password" => "new password of the user", "re_entered_new_password" => "re entered new password of the user"]
    // displaying of the popup should be handled by the front end
    public function changePassword(array $args): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // verify the current password 
            if (
                array_key_exists("old_password", $args) &&
                password_verify($args["old_password"], $this->user->getUserData()->password)
            ) {
                $this->sendJsonResponse(
                    status: "success",
                    content: array("message" => "User verified")
                );
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array("message" => "The password provided doesn't match")
                );
            }
        } else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            // change the password
            if (
                array_key_exists("new_password", $args) &&
                array_key_exists("re_entered_new_password", $args)
            ) {
                $this->validator = new Validator();
                $this->validator->validate($args, "password_change");
                if ($this->validator->getPassed()) {
                    if ($this->user->updatePassword($this->user->getUserData()->id, $args["new_password"]))
                        $this->sendJsonResponse(
                            status: "success",
                            content: array("message" => "Password successfully changed")
                        );
                    else $this->sendJsonResponse(
                        status: "internal_server_error",
                        content: array("message" => "Password cannot be changed")
                    );
                } else {
                    $this->sendJsonResponse(
                        status: "success",
                        content: array(
                            "message" => "Input validation failed",
                            "errors" => $this->validator->getErrors()
                        )
                    );
                }
            } else {
                $this->sendJsonResponse(
                    status: "error",
                    content: array("message" => "Bad request, missing arguments")
                );
            }
        } else {
            $this->sendResponse(
                view: "/errors/404.html",
                status: "error",
                content: array("message" => "Bad request")
            );
        }
    }

    public function getNotifications():array|object
    {
        $payload = $this->userAuth->getCredentials();

        $notification = new Notification();
        $user = new User();
        $project = new Project($payload->id);
        $payload = $this->userAuth->getCredentials();

        $condition = " WHERE id IN (SELECT notification_id FROM notification_recievers WHERE member_id = " .$payload->id. " AND isRead = 0)";
        $notifications = [];
        try {
            $notifications = $notification->getNotifications($condition);

        } catch (\Throwable $th) {
            throw $th;
        }

        if($notifications){
            foreach($notifications as $notification) {
                $user->readUser("id", $notification->sender_id);
                $sender = $user->getUserData();
                $notification->sender_name = $sender->first_name . " " . $sender->last_name;
                $notification->project_name = $project->getProject(array("id" => $notification->project_id))->project_name;
                $notification->sender_profile = $sender->profile_picture;
            }
        }

        $this->sendJsonResponse(
            status: "success",
            content: [
                "message" => $notifications
            ]
        );
    }

    public function getTaskDeadlines(){
        $payload = $this->userAuth->getCredentials();

        $task = new Task();
        $tasks = $task->getTasks(array("status" => "ONGOING", "member_id" => $payload->id), array("status", "member_id"));
        // var_dump($tasks);
        $deadlines = [];
        if($tasks){
            foreach($tasks as $taskData) {
                if( explode(" ", $taskData->deadline)[0] != "0000-00-00"){
                    $deadlines[] = array("deadline" => explode(" ", $taskData->deadline)[0], "task_name" => $taskData->task_name);
                }
            }
        }

        return array("taskDeadlines" => $deadlines);
    }
}
