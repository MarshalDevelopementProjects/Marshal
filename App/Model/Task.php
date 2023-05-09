<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Task
{
    private CrudUtil $crud_util;
    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createTask(array $args, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }
        if($args['project_id'] != $_SESSION['project_id']) {
            return false;
        }else{
            $keyCount = count($keys);

            $sql = "INSERT INTO task (";

            for ($i = 0; $i < $keyCount; $i++) {
                $key = $keys[$i];
                $sql .= '`' . $key . '`';

                if ($i != $keyCount - 1) {
                    $sql .= ", ";
                }
            }
            $sql .= ") VALUES (";
            for ($i = 0; $i < $keyCount; $i++) {
                $key = $keys[$i];
                $sql .= ':' . $key;

                if ($i != $keyCount - 1) {
                    $sql .= ", ";
                }
            }
            $sql .= ')';
            try {
                $this->crud_util->execute($sql, $args);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }
        
    }

    public function getAllTasks(array $args = array()): object|bool|array
    {
        $task_types = array("project", "group");
        if (!array_key_exists('project_id', $args) || !array_key_exists('task_type', $args) || $args['project_id'] != $_SESSION['project_id'] || !in_array($args['task_type'], $task_types)){
            return false;
        }

        if ($args['task_type'] === 'group') {
            if(!array_key_exists('group_id', $args) || $args['group_id'] != $_SESSION['group_id']){
                return false;
            }
            $sql_string = "SELECT * FROM task WHERE task_id IN(SELECT task_id FROM group_task WHERE group_id = :group_id) AND project_id = :project_id AND task_type = :task_type";
        } else {
            $sql_string = "SELECT * FROM task WHERE project_id = :project_id AND task_type = :task_type";
        }

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getResults();
        } else {
            return false;
        }
    }
    public function getTasks(array $args, array $keys)
    {
        $status_array = array("TO-DO", "ONGOING", "PENDING", "DONE");
        foreach ($keys as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }

        // check arguments validity
        if(in_array('project_id', $keys)){
            if($args['project_id'] != $_SESSION['project_id']) {
                return false;
            }
        }elseif(in_array('status', $keys)){
            if(!in_array($args['status'], $status_array)){
                return false;
            }
        }

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
                return $result->getResults();
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getTask(array $args, array $keys): object|bool|array
    {
        $status_array = array("TO-DO", "ONGOING", "PENDING", "DONE");
        foreach ($keys as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return array();
            }
        }

        // check arguments validity
        if(in_array('project_id', $keys)){
            if($args['project_id'] != $_SESSION['project_id']) {
                return array();
            }
        }elseif(in_array('status', $keys)){
            if(!in_array($args['status'], $status_array)){
                return array();
            }
        }

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
                return array();
            }
        } catch (\Exception $exception) {
            return array();
        }
    }

    public function updateTask(array $args, array $updates, array $conditions): object|bool|array
    {
        foreach ($updates as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }
        foreach ($conditions as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }

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
            return true;
        } catch (\Exception $exception) {
            // throw $exception;
            return false;
        }
    }

    public function completeTask(array $args = array()): bool
    {
        if($this->getTask(array("task_id" => $args['task_id']), array("task_id"))){
            $sql_string = "INSERT INTO completedtask (`task_id`, `confirmation_type`, `confirmation_message`, `date`, `time`) VALUES (:task_id, :confirmation_type, :confirmation_message, :date, :time)";

            try {
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }else{
            return false;
        }
        
    }

    public function getTaskCompletedDetails(array $args = array()): object|bool|array
    {
        if($args == array()){
            return false;
        }else{
            $sql_string = "SELECT * FROM completedtask WHERE task_id = :task_id";

            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                return $result->getFirstResult();
            } else {
                return false;
            }
        }
    }

    public function addGroupToTask(array $args = array()): bool
    {
        if(!in_array('group_id', $args)|| !in_array('task_id',$args)){
            return false;
        }
        if($this->getTask(array("task_id" => $args['task_id']), array("task_id"))){
            $sql = "INSERT INTO group_task (`task_id`, `group_id`) VALUES (:task_id, :group_id)";

            try {
                $this->crud_util->execute($sql, $args);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        }else{
            return false;
        }
        
    }
}
