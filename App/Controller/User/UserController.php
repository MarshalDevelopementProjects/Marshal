<?php

namespace App\Controller\User;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;
use App\Model\User;
use App\Controller\Controller;
use App\Model\Project;
use Core\Validator\Validator;
use Core\FileUploader;

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
                    if ($credentials->id) $this->user = new User($credentials->id);
                    else $this->user = new User($credentials->id);
                }
            } else {
                $this->sendResponse(
                    view: "/user/login.html",
                    status: "unauthorized"
                );
            }
            $this->validator = new Validator();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function auth()
    {
        return $this->userAuth->isLogged();
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
        $this->sendResponse(
            view: "/user/dashboard.html",
            status: "success",
            content: array("message" => "Welcome")
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
            return $this->sendJsonResponse("success", array("message" => "Project successfully created", "projects" => $results));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function viewProjects()
    {
        try {
            $payload = $this->userAuth->getCredentials(); // get the payload content
            $project = new Project($payload->id);
            if ($project->readProjectsOfUser($payload->id)) {
                return $this->sendJsonResponse(
                    "success",
                    array(
                        "message" => "user projects",
                        "projects" => $project->getProjectData() // this is an array of objects
                    )
                );
            } else {
                // if there are no projects then an empty string is send as the message
                $this->sendJsonResponse("success");
            }
        } catch (\Exception $exception) {
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
                        $this->sendResponse(
                            view: "/project_leader/dashboard.html",
                            status: "success",
                            content: $project->readProjectsOfUser(
                                member_id: $payload->id,
                                project_id: $data["id"]
                            ) ? $project->getProjectData() : array()
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
                        $this->sendResponse(
                            view: "/project_member/dashboard.html",
                            status: "success",
                            content: $project->readProjectsOfUser(
                                member_id: $payload->id,
                                project_id: $data["id"]
                            ) ? $project->getProjectData() : array()
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

    public function viewProfile()
    {
        try {
            $user_data = $this->user->getUserData();
            // send the statistical data as well next time
            $this->sendResponse(
                view: "/user/profilepage.html",
                status: "success",
                content: [
                    "message" => "Successful",
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
}
