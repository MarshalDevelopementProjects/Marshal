<?php

namespace App\Controller\Project;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
// use App\Model\Project;
use App\Model\Task;
use App\Model\User;
// use Core\Validator\Validator;
// use App\Controller;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectController extends UserController{

    public function __construct(){
        $this->userAuth = new UserAuthController();
    }
    public function defaultAction(Object|array|string|int $optional = null)
    {

    }

    public function getProjectTasks(array $args = array()){
        $task = new Task();
        $user = new User();
        // get all tasks related to this project
        $tasks = $task->getAllTasks($args);

        if($tasks){
            // divide the tasks by status
            $todoTasks = array();
            $ongoingTasks = array();
            $reviewTasks = array();
            $doneTasks = array();

            foreach($tasks as $task){

                switch ($task->status) {
                    case 'TO-DO':
                        $todoTasks[] = $task;
                        break;
                    case 'ONGOING':
                        $payload = $this->userAuth->getCredentials();
                        $user_id = $payload->id;

                        $userData = $user->readUser("id", $task->memberId);
                        $userData = $user->getUserData();

                        $task->profile = $userData->profile_picture;
                        $task->userId = $user_id;

                        $ongoingTasks[] = $task;
                        break;
                    case 'REVIEW':
                        $reviewTasks[] = $task;
                        break;
                    case 'DONE':
                        $doneTasks[] = $task;
                        break;
                    default:
                        break;
                }
            }

            $projectTasks = array(
                "todoTasks" => $todoTasks,
                "ongoingTasks" => $ongoingTasks,
                "reviewTasks" => $reviewTasks,
                "doneTasks" => $doneTasks
            );
            return $projectTasks;
        }else{
            return array();
        }
        
    }
}
