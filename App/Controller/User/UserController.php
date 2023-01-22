<?php

namespace App\Controller\User;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;
use App\Model\User;
use App\Controller\Controller;
use App\Model\Project;
use Core\Validator\Validator;

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
                $credentials = $this->userAuth->getCredentials();
                if ($credentials->id) $this->user = new User($credentials->id);
                else $this->user = new User($credentials->id);
            } else {
                $this->sendResponse(
                    view: "/user/login.html",
                    status: "unauthorized"
                );
                exit;
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
        $payload = $this->userAuth->getCredentials(); // get the payload content
        try {
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
        } catch (\Exception $exception) {
            $this->sendJsonResponse("forbidden", array("message" => "User cannot be identified"));
        }
    }

    public function viewProfile()
    {
        throw new \Exception("Not implemented");
    }

    public function editProfile()
    {
        throw new \Exception("Not implemented");
    }

    public function getUserData()
    {
        if ($this->user) {
            return $this->user->getUserData();
        }
        return null;
    }
}
