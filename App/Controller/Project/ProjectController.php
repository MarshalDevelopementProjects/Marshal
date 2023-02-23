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

class ProjectController
{

    public function __construct()
    {
    }
    public function defaultAction(Object|array|string|int $optional = null)
    {
    }

    public function getProjectTasks(array $args = array(), $user_id = null)
    {
        $newTask = new Task();
        $user = new User();
        // get all tasks related to this project
        $tasks = $newTask->getAllTasks($args);

        if ($tasks) {
            // divide the tasks by status
            $todoTasks = array();
            $ongoingTasks = array();
            $reviewTasks = array();
            $doneTasks = array();

            foreach ($tasks as $task) {

                switch ($task->status) {
                    case 'TO-DO':
                        $todoTasks[] = $task;
                        break;
                    case 'ONGOING':

                        $userData = $user->readUser("id", $task->memberId);
                        $userData = $user->getUserData();

                        if($userData){
                            $task->profile = $userData->profile_picture;
                        }
                        $task->userId = $user_id;

                        $ongoingTasks[] = $task;
                        break;
                    case 'REVIEW':

                        $userData = $user->readUser("id", $task->memberId);
                        $userData = $user->getUserData();

                        if($userData){
                            $task->profile = $userData->profile_picture;
                        }
                        
                        $task->userId = $user_id;

                        // get completed data

                        // $taskData = $newTask->getTask(array("project_id" => $_SESSION['project_id'], "task_name" => "Build API")); 
                        $completedData = $newTask->getTaskCompletedDetails(array("taskId" => $task->task_id));
                        $task->completeTime = $completedData->date . ' ' . $completedData->time;
                        $task->confirmationMessage = $completedData->confirmation_message;

                        $reviewTasks[] = $task;
                        break;
                    case 'DONE':
                        $userData = $user->readUser("id", $task->memberId);
                        $userData = $user->getUserData();

                        if($userData){
                            $task->profile = $userData->profile_picture;
                        }
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
        } else {
            return array();
        }
    }
}
