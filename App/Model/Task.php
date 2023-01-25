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

}
