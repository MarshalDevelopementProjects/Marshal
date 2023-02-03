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

    public function getTask(array $args, array $keys){
        $keyCount = count($keys);

        $sql = "SELECT * FROM task WHERE ";
        for ($i = 0; $i < $keyCount; $i++) {
            $key = $keys[$i];
            $sql .= $key . " = :" . $key;

            if ($i != $keyCount - 1) {
                $sql .= " AND ";
            }
        }

        try {
            $result = $this->crud_util->execute($sql, $args);
            if ($result->getCount() > 0) {
                return $result->getFirstResult();
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function updateTask(array $args, array $updates, array $conditions)
    {
        $updateFieldsCount = count($updates);
        $conditionFieldsCount = count($conditions);

        $sql = "UPDATE task SET ";
        for ($i = 0; $i < $updateFieldsCount; $i++) {
            $updateField = $updates[$i];
            $sql .= '`' . $updateField . "` = :" . $updateField;

            if ($i != $updateFieldsCount - 1) {
                $sql .= ", ";
            }
        }
        $sql .= " WHERE ";

        for ($j = 0; $j < $conditionFieldsCount; $j++) {
            $conditionField = $conditions[$j];
            $sql .= '`' . $conditionField . "` = :" . $conditionField;

            if ($j != $conditionFieldsCount - 1) {
                $sql .= " AND ";
            }
        }

        try {
            $this->crud_util->execute($sql, $args);
            return $sql;
        } catch (\Exception $exception) {
            throw $exception;
            // return false;
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
