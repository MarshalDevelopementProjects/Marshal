<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

// TODO: ADD A METHOD TO CALCULATE THE PROJECT PROGRESS
// TODO: ADD SOME DETAILS TO THIS PROGRESS AS WELL

use App\CrudUtil\CrudUtil;
use Exception;

class Project implements Model
{
    private CrudUtil $crud_util;
    private Object|array $project_data; // object or an array of object
    private object|array|null $project_member_data;

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

    public function getProjectUsers(string $condition){
        $sql = "SELECT * FROM project_join " . $condition;

        // var_dump($sql);
        try {
            $result = $this->crud_util->execute($sql);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update($columns, $conditions, $args)
    {
        $sql = "UPDATE project SET ";

        for($i=0; $i<count($columns); $i++){
            $sql .= $columns[$i] . ' = :' . $columns[$i];
            if($i != count($columns) -1){
                $sql .= ', ';
            }
        }
        $sql .= " WHERE ";

        for ($i = 0; $i < count($conditions); $i++) {
            $key = $conditions[$i];
            $sql .= $key . " = :" . $key;

            if ($i != count($conditions) - 1) {
                $sql .= " AND ";
            }
        }

        // var_dump($sql);
        try {
            $this->crud_util->execute($sql, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function delete(string $_id = null)
    {
        $sql = "DELETE FROM project WHERE ";

        for ($i = 0; $i < count($conditions); $i++) {
            $key = $conditions[$i];
            $sql .= $key . " = :" . $key;

            if ($i != count($conditions) - 1) {
                $sql .= " AND ";
            }
        }
        try {
            $this->crud_util->execute($sql, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectData()
    {
        return $this->project_data;
    }

    /**
     * @throws Exception
     */
    public function getProjectMembers(string|int|null $project_id = null): bool
    {
        try {
            $sql_string = "SELECT `user`.`username` AS `username`,
                           `user`.user_status AS `status`,
                           `user`.user_state AS `state`,
                           `user`.profile_picture AS `profile_picture`,
                           `project_join`.role AS `role`
                            FROM `user`
                            INNER JOIN
                           `project_join` ON
                           `project_join`.`project_id` = :project_id AND 
                           `user`.`id` = `project_join`.`member_id`";
            $this->crud_util->execute($sql_string, ["project_id" => $project_id !== null ? $project_id : $this->project_data->id]);
            if (!$this->crud_util->hasErrors()) {
                $this->project_member_data = $this->crud_util->getResults();
                return true;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectMembersByRole(string|int $project_id, string $role): bool
    {
        if ($project_id && $role) {
            try {
                // get the details of the clients
                $sql_string = "SELECT u.id AS id, u.username AS username, u.profile_picture AS profile_picture, p_j.role AS role FROM project_join p_j JOIN user u ON p_j.member_id = u.id WHERE p_j.project_id = :project_id AND p_j.role = :role";
                $this->crud_util = $this->crud_util->execute($sql_string, ["project_id" => $project_id, "role" => $role]);
                if (!$this->crud_util->hasErrors()) {
                    $this->project_member_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw  $exception;
            }
        }
        return false;
    }

    public function getProjectProgress(string|int $project_id): float|bool|int
    {
        try {

            /*
             * Example query
              SELECT
               COUNT(CASE WHEN `t`.`status` = 'DONE' THEN 1 END) AS `comepleted_tasks`,
               COUNT(`t`.`task_id`) AS `all_tasks`
               FROM
                `task` `t`
               WHERE
                `t`.`project_id` = 4;
             */
            $sql_string = "
                   SELECT
                   COUNT(CASE WHEN `t`.`status` = 'DONE' THEN 1 END) AS `no_of_completed_tasks`,
                   COUNT(`t`.`task_id`) AS `no_of_tasks`
                   FROM
                       `task` `t`
                   WHERE
                       `t`.`project_id` = :project_id AND `t`.task_type = 'project'
                   ";
            $this->crud_util->execute($sql_string, ["project_id" => $project_id]);
            if (!$this->crud_util->hasErrors()) {
                return $this->crud_util->getFirstResult()->no_of_tasks ?
                    ($this->crud_util->getFirstResult()->no_of_completed_tasks / $this->crud_util->getFirstResult()->no_of_tasks) * 100 :
                    0;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function getProjectStatistics(string|int $project_id): bool
    {
        $task_details_sql_string = "
                   SELECT
                   COUNT(CASE WHEN `t`.`status` = 'DONE' THEN 1 END) AS `no_of_completed_tasks`,
                   COUNT(CASE WHEN `t`.`status` = 'TO-DO' THEN 1 END) AS `no_of_todo_tasks`,
                   COUNT(CASE WHEN `t`.`status` = 'PENDING' THEN 1 END) AS `no_of_pending_tasks`,
                   COUNT(CASE WHEN `t`.`status` = 'ONGOING' THEN 1 END) AS `no_of_ongoing_tasks`,
                   COUNT(CASE WHEN `t`.`status` = 'REVIEW' THEN 1 END) AS `no_of_reviewed_tasks`,
                   COUNT(CASE WHEN `t`.`priority` = 'high' THEN 1 END) AS `no_of_high_priority_tasks`,
                   COUNT(CASE WHEN `t`.`priority` = 'medium' THEN 1 END) AS `no_of_medium_priority_tasks`,
                   COUNT(CASE WHEN `t`.`priority` = 'low' THEN 1 END) AS `no_of_low_priority_tasks`,
                   COUNT(`t`.`task_id`) AS `total_no_of_tasks`
                   FROM
                       `task` `t`
                   WHERE
                       `t`.`project_id` = :project_id AND `t`.task_type = 'project'
                   ";
        $stat_per_week_sql_string = "
                    SELECT
                    CONCAT(WEEK(`c_t`.`date`)  - WEEK(`p`.`start_on`) + 1) AS `week`,
                    COUNT(*) AS `no_of_completed_tasks`
                    FROM `completedtask` `c_t`
                    INNER JOIN `task` `t` ON `t`.task_id = `c_t`.task_id
                    INNER JOIN `project` `p` ON `t`.project_id = `p`.id
                    WHERE `t`.status = 'DONE'
                      AND `t`.`project_id` = :project_id
                      AND `t`.`task_type` = 'project'
                      AND `c_t`.`date` < `p`.`end_on`
                    GROUP BY `week`
                    ORDER BY WEEK(`c_t`.`date`)   - WEEK(`p`.`start_on`) + 1;
        ";
        $this->crud_util->execute($task_details_sql_string, ["project_id" => $project_id]);
        if (!$this->crud_util->hasErrors()) {
            $this->project_data["task_details"] = $this->crud_util->getFirstResult();
            $this->crud_util->execute($stat_per_week_sql_string, ["project_id" => $project_id]);
            if (!$this->crud_util->hasErrors()) {
                $this->project_data["stat_per_week_details"] = $this->crud_util->getFirstResult();
                return true;
            }
        }
        return false;
    }

    public function getProjectMemberData(): array|null|object|bool
    {
        return $this->project_member_data;
    }
}
