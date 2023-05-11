<?php

namespace App\Controller\Project;

use App\Model\Task;
use App\Model\User;
use App\Model\Project;

require __DIR__ . '/../../../vendor/autoload.php';

class ProjectController
{

    public function __construct()
    {
    }

    public function getProjectTasks(array $args = array(), $user_id = null)
    {
        $task_types = array("project", "group");
        $newTask = new Task();
        $user = new User();
        $project = new Project($user_id);

        // var_dump($args['task_type']);
        if($args == array() || $user_id == null || !in_array($args['task_type'], $task_types) || !$project->readUserRole(member_id: $user_id, project_id: $_SESSION['project_id'])){
            return array();
        }else{
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

                        $userData = $user->readUser("id", $task->member_id);
                        $userData = $user->getUserData();

                        if ($userData) {
                            $task->profile = $userData->profile_picture;
                        }
                        $task->userId = $user_id;

                        $ongoingTasks[] = $task;
                        break;
                    case 'REVIEW':

                        $userData = $user->readUser("id", $task->member_id);
                        $userData = $user->getUserData();

                        if ($userData) {
                            $task->profile = $userData->profile_picture;
                        }

                        $task->userId = $user_id;

                        // get completed data

                        // $taskData = $newTask->getTask(array("project_id" => $_SESSION['project_id'], "task_name" => "Build API")); 
                        $completedData = $newTask->getTaskCompletedDetails(array("task_id" => $task->task_id));
                        if($completedData){
                            $task->completeTime = $completedData->date . ' ' . $completedData->time;
                            $task->confirmationMessage = $completedData->confirmation_message;
                        }

                        $reviewTasks[] = $task;
                        break;
                    case 'DONE':
                        $userData = $user->readUser("id", $task->member_id);
                        $userData = $user->getUserData();

                        if ($userData) {
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
}
