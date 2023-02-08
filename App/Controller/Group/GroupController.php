<?php

namespace App\Controller\Group;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\Task;
use App\Model\User;


require __DIR__ . '/../../../vendor/autoload.php';

class GroupController
{

    public function __construct()
    {
    }
    public function defaultAction(object|array |string|int $optional = null)
    {
    }

    public function getGroupTasks(array $args = array(), $user_id = null){
        $newTask = new Task();
    
        // get all tasks related to this project
        $tasks = $newTask->getAllTasks($args);

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