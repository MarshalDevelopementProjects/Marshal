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
    private UserAuthController $userAuth;
    private User $user;
    private Validator $validator;

    public function __construct(string|int $user_id = null)
    {
        try {
            parent::__construct();
            $this->userAuth = new UserAuthController();
            if ($this->auth()) {
                $credentials = $this->userAuth->getCredentials();
                if ($credentials->id) $this->user = new User($credentials->id);
                else $this->user = new User($credentials->id);
            } else if ($user_id) {
                $this->user = new User($user_id);
            } else {
                $this->user = new User();
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

    public function defaultAction()
    {
        if ($this->auth()) {
            $this->sendResponse(
                view: "/user/dashboard.html",
                status: "success"
            );
        } else {
            $this->sendResponse(
                view: "/user/login.html",
                status: "unauthorized"
            );
        }
    }

    public function createProject(array $project_details = array())
    {
        if ($this->auth()) {
            $payload = $this->userAuth->getCredentials(); // get the payload content

            // add owner_id
            $project_details["created_by"] = $payload->id;

            // add the data to the database
            try {
                $project = new Project($payload->id);
                $project->createProject($project_details);
                $results = $project->getProjectData();

                foreach ($results as $result) {
                    unset($result->owner_id); // remove the project id from the data sent back need the project id
                }

                return $this->sendJsonResponse("success", array("message" => "Project successfully created", "projects" => $results));
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            return $this->sendJsonResponse("error", array("message" => "Input validation errors", "errors" => $this->validator->getErrors()));
        }
    }

    public function viewProjects()
    {
        if ($this->auth()) {
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
        } else {
            $this->sendJsonResponse("forbidden", array("message" => "Access denied"));
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

    public function findUser(string $user_id)
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
