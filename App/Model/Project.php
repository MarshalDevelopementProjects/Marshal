<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Project implements Model
{
    private CrudUtil $crud_util;
    private Object|array $project_data; // object or an array of object 

    public function __construct(private string|int $member_id, string|int $project_id = null)
    {
        try {
            $this->crud_util = new CrudUtil();
            if ($project_id != null) {
                // CAUTION if the project is not found an exception is thrown
                if (!$this->readProjectsOfUser($member_id, $project_id)) {
                    throw new \Exception("Project(s) of the requested user cannot be found");
                }
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createProject(array $args = array()): bool
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO project(`created_by`, `project_name`, `description`, `field`)
                           VALUES(:created_by, :project_name, :description, :field)";
            if (array_key_exists("start_on", $args) && array_key_exists("end_on", $args)) {
                $sql_string = "INSERT INTO project(`created_by`, `project_name`, `description`, `field`, `start_on`, `end_on`)
                           VALUES(:created_by, :project_name, :description, :field, :start_on, :end_on)";
            } else if (array_key_exists("end_on", $args)) {
                $sql_string = "INSERT INTO project(`created_by`, `project_name`, `description`, `field`, `end_on`)
                           VALUES(:created_by, :project_name, :description, :field, :end_on)";
            }
            try {
                $this->crud_util->execute($sql_string, $args);
                $this->readProjectsOfUser(member_id: $args["created_by"]);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    public function readProjectsOfUser(string|int $member_id, string|int $project_id = null): bool
    {
        try {
            $sql_string = "SELECT * FROM `project` WHERE `id` IN (SELECT `project_id` FROM `project_join` WHERE `member_id` = :member_id)";
            $args = array("member_id" => $member_id);
            if ($project_id) {
                $sql_string = "SELECT * FROM `project` WHERE `id` IN 
                               (SELECT `project_id` FROM `project_join` WHERE `member_id` = :member_id AND `project_id` = :project_id)";
                $args["project_id"] = $project_id;
            }
            // execute the query
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                $this->project_data = $result->getResults(); // get all the results or just one result this is an array of objects
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
        return false;
    }

    public function update(string $_id = null, array $_array = array())
    {
        throw new \Exception("Not implemented yet");
    }

    public function delete(string $_id = null)
    {
        throw new \Exception("Not implemented yet");
    }

    public function getProjectData()
    {
        return $this->project_data;
    }
}
