<?php

namespace App\Controller\User;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;
use App\Model\User;
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
    public function defaultAction(Object|array|string|int $optional = null)
    {
        // get relevant projects
        $payload = $this->userAuth->getCredentials(); // get the payload content
        $project = new Project($payload->id);

        $Projects = array();
        if ($project->readProjectsOfUser($payload->id)) {
            $Projects = $project->getProjectData();
        }
        // if ($Projects == null) {
        //     $Projects = array();
        // }

        $this->sendResponse(
            view: "/user/dashboard.html",
            status: "success",
            content: $Projects
        );
    }

    public function createProject(array $args = array())
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
            $this->sendJsonResponse("success", array("message" => "Project successfully created", "projects" => $results));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function viewProjects()
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

    public function goToProject(array $data)
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

                        $this->sendResponse(
                            view: "/project_leader/dashboard.html",
                            status: "success",
                            content: $projectController->getProjectTasks($args, $payload->id)
                        );
                        break;
                    case 'CLIENT':
                        $this->sendResponse(
                            view: "/client/dashboard.html",
                            status: "success",
                            content: $project->readProjectsOfUser(
                                member_id: $payload->id,
                                project_id: $data["id"]
                            ) ? $project->getProjectData() : array()
                        );
                        break;
                    case 'MEMBER':

                        $args = array(
                            "project_id" => $_SESSION['project_id'],
                            "task_type" => "project"
                        );
                        $projectController = new ProjectController();

                        $this->sendResponse(
                            view: "/project_member/dashboard.html",
                            status: "success",
                            content: $projectController->getProjectTasks($args, $payload->id)
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
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    public function viewProfile()
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
                        "bio" => $user_data->bio
                    ),
                    "other_info" => array()
                )
            );
        } catch (Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    public function uploadProfilePicture()
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

    public function editProfile(array $args = array())
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

    public function getUserData()
    {
        if ($this->user) {
            return $this->user->getUserData();
        }
        return null;
    }
    public function getNotifications()
    {

        $payload = $this->userAuth->getCredentials();
        $userId = $payload->id;

        $notification = new Notification();
        $args = array(
            "memberId" => $userId
        );
        $notifications = $notification->getNotificationsOfUser($args);

        foreach ($notifications as $notification) {
            $senderId = $notification->senderId;

            // get sender name
            $sender = new User($senderId);
            $sendername = $sender->getUserData()->first_name . ' ' . $sender->getUserData()->last_name;
            $notification->senderId = $sendername;
            $notification->sendTime = explode(' ', $notification->sendTime)[0];
        }

        // $projectId = $notifications[0]->projectId;

        // $args = array(
        //     "id" => $projectId
        // );

        if ($notifications) {
            echo (json_encode(array("message" => $notifications)));
        } else {
            echo (json_encode(array("message" => null)));
        }
    }

    public function userJoinOnProject()
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

        if ($project->joinProject($args) && $this->readNotification($notificationId)) {
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

    public function clickOnNotification()
    {
        $notificationId = $_GET['data'];
        $this->readNotification($notificationId);

        // $this->defaultAction();

    }

    public function readNotification($notificationId)
    {

        $payload = $this->userAuth->getCredentials();
        $userId = $payload->id;

        $notification = new Notification();
        $conditions = array(
            "notificationId" => $notificationId,
            "memberId" => $userId
        );

        if ($notification->readNotification($conditions)) {
            return true;
        } else {
            return false;
        }
    }

    public function sendResponseNotification($notificationId, $projectId)
    {
        try {
            $notification = new Notification();
            $args = array(
                "id" => $notificationId
            );
            $notificationData = $notification->getNotificationData($args);

            // get received user id
            // $user = new User();
            // $user->readUser("id", $notificationData['senderId']);
            // $receivedUser = $user->getUserData();


            $payload = $this->userAuth->getCredentials();
            $user_id = $payload->id;

            $date = date("Y-m-d H:i:s");

            $args = array(
                "projectId" => $projectId,
                "message" => "I accept your invitation, So now I will a member of your project",
                "type" => "notification",
                "senderId" => $user_id,
                "sendTime" => $date
            );

            // set notified members
            // get notification id
            $notification = new Notification();
            $notification->createNotification($args);

            $conditions = array(
                "projectId" => $projectId,
                "senderId" => $user_id,
                "sendTime" => $date
            );

            $newNotification = $notification->getNotificationData($conditions);
            $newNotificationId = $newNotification[0]->id;

            $receivedUserId = 1;
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

    public function sketch()
    {
        $this->sendResponse(
            view: "/user/sketch.html",
            status: "success",
        );
    }
    // this function will be used to generate reports
    public function generateReport()
    {
    }

    // change the user password
    //
    // for the post request, need the following format 
    // ["old_password" => "old password of the user"]
    // 
    // for the put request, need the following format
    // ["new_password" => "new password of the user", "re_entered_new_password" => "re entered new password of the user"]
    // displaying of the popup should be handled by the front end
    public function changePassword(array $args)
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
}
