<?php

namespace App\Controller\User;

require __DIR__ . "/../../../vendor/autoload.php";

use App\Controller\Authenticate\UserAuthController;
use App\Model\User;
use App\Controller\Controller;
use App\Model\Project;
use App\Model\Notification;
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
    public function getNotifications(){

        $payload = $this->userAuth->getCredentials();
        $userId = $payload->id;

        $notification = new Notification();
        $args = array(
            "memberId" => $userId
        );
        $notifications = $notification->getNotificationsOfUser($args);
        
        foreach($notifications as $notification){
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
        
        if($notifications){
            echo (json_encode(array("message" => $notifications)));
        }else{
            echo (json_encode(array("message" => null)));
        }
        
    }

    public function userJoinOnProject(){
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

        if($project->joinProject($args) && $this->readNotification($notificationId)){
            $this->sendResponse(
                view: "/user/login.html",
                status: "success"
            );
        }else{
            $this->sendResponse(
                view: "/user/signup.html",
                status: "success"
            );
        }
        // we should send the notification to leader to inform our response
        $this->sendResponseNotification($notificationId, $projectId);
    }

    public function clickOnNotification(){
        $notificationId = $_GET['data'];
        $this->readNotification($notificationId);

        // $this->defaultAction();

    }

    public function readNotification($notificationId){

        $payload = $this->userAuth->getCredentials();
        $userId = $payload->id;

        $notification = new Notification();
        $conditions = array(
            "notificationId" => $notificationId,
            "memberId" => $userId
        );
        
        if($notification->readNotification($conditions)){
            return true;
        }else{
            return false;
        }
    }

    public function sendResponseNotification($notificationId, $projectId){
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

}
