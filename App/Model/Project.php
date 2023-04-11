<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Project implements Model
{
    private CrudUtil $crud_util;
    private Object|array $project_data; // object or an array of object 

    public function __construct(string|int $member_id = null, string|int $project_id = null)
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
                $sql_string = "SELECT * FROM `project` WHERE `id` IN (SELECT `project_id` FROM `project_join` WHERE `project_id` = :project_id AND `member_id` = :member_id);";
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
    }

    public function readUserRole(string|int $member_id, string|int $project_id): bool
    {
        try {
            $sql_string = "SELECT `project_join`.`role` FROM `project_join` WHERE `project_id` = :project_id AND `member_id` = :member_id";
            $args = array("member_id" => $member_id, "project_id" => $project_id);
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
    }

    public function getProjectDetailsOfUser(string|int $user_id, string|int $project_id): bool
    {
        try {
            $sql_string = "SELECT `project`.`id`,`project`.`project_name`, `project`.`description`, `project`.`start_on`, `project`.`end_on`, `project_join`.`member_id`, `project_join`.`role`
                       FROM `project` INNER JOIN `project_join` ON `project_join`.`member_id` = 1 AND `project`.`id` = 2 LIMIT 1";
            $args = array("member_id" => $user_id, "project_id" => $project_id);
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
    }
    public function joinProject(array $args = array())
    {
        $sql_string = "INSERT INTO project_join(`project_id`, `member_id`, `role`, `joined`) VALUES (:project_id, :member_id, :role, :joined)";

        try {
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getProject(array $args = array()): object|bool|array
    {
        $sql_string = "SELECT * FROM project WHERE id = :id";

        try {
            $result = $this->crud_util->execute($sql_string, $args);
            return $result->getFirstResult();
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function readProjectData(string|int $project_id): bool
    {
        try {
            $sql_string = "SELECT * FROM `project` WHERE `id` = :id";
            $args = array("id" => $project_id);
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



































































































    public function getAllMembersOfProject(string|int $project_id): bool
    {
        return false;
    }
}
