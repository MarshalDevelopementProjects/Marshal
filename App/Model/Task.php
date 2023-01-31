<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Task implements Model
{
    private CrudUtil $crud_util;
    public function __construct(){
        try {
            $this->crud_util = new CrudUtil();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createTask(array $args = array()){
        $sql_string = "INSERT INTO task (`project_id`, `description`, `deadline`, `task_name`, `priority`, `status`) VALUES (:project_id, :description, :deadline, :task_name, :priority, :status)";
        
        try {
            $this->crud_util->execute($sql_string, $args);
            
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getAllTasks(array $args = array()){
        $sql_string = "SELECT * FROM task WHERE project_id = :project_id";

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getResults();
        } else {
            return false;
        }   
    }

    public function getTask(array $args = array()){
        $sql_string = "SELECT * FROM task WHERE project_id = :project_id AND task_name = :task_name";

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getFirstResult();
        } else {
            return false;
        }   
    }
    public function pickupTask(array $args = array()){
        $sql_string = "UPDATE task SET `status` = :status , `memberId` = :memberId WHERE `project_id` = :project_id AND `task_name` = :task_name";

        try {
            $this->crud_util->execute($sql_string, $args);
            
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function rearangeTask(array $args = array(), $atTodo = false){
        if($atTodo){
            $sql_string = "UPDATE task SET `status` = :status , `memberId` = NULL WHERE `project_id` = :project_id AND `task_name` = :task_name";
        }else{
            $sql_string = "UPDATE task SET `status` = :status WHERE `project_id` = :project_id AND `task_name` = :task_name";
        }

        try {
            $this->crud_util->execute($sql_string, $args);
            
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function completeTask(array $args = array()){
        $sql_string = "INSERT INTO completedtask (`taskId`, `confirmation_type`, `confirmation_message`, `date`, `time`) VALUES (:taskId, :confirmation_type, :confirmation_message, :date, :time)";

        try {
            $this->crud_util->execute($sql_string, $args);
            
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
    public function updateTaskState(array $args = array()){
        $sql_string = "UPDATE task SET `status` = :status WHERE project_id = :project_id AND task_name = :task_name";
        try {
            $this->crud_util->execute($sql_string, $args);
            
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getTaskCompletedDetails(array $args = array()){
        $sql_string = "SELECT * FROM completedtask WHERE taskId = :taskId";

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getFirstResult();
        } else {
            return false;
        }   
    }

}
