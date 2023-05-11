<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;
use Exception;

class Group
{

    private array|object|null $group_member_data;
    private CrudUtil $crud_util;
    private array|object|null $group_data;

    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createGroup(array $args, array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
         
        $keyCount = count($keys);

        $sql = "INSERT INTO groups (";

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

    public function getAllGroups(array $args, array $keys): object|bool|array
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        
        $keyCount = count($keys);
        $sql = "SELECT * FROM groups WHERE ";

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
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getGroup(array $args, array $keys): object|bool|array
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        $keyCount = count($keys);
        $sql = "SELECT * FROM groups WHERE ";

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
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateGroup(array $args, array $updates, array $conditions): object|bool|array
    {
        foreach ($updates as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        foreach ($conditions as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }

        $updateFieldsCount = count($updates);
        $conditionFieldsCount = count($conditions);

        $sql = "UPDATE groups SET ";
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

    public function addGroupMember(array $args, array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        $keyCount = count($keys);

        $sql = "INSERT INTO group_join (";

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

    public function getGroupMember(array $args, array $keys): bool|object|array
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        $keyCount = count($keys);
        $sql = "SELECT * FROM group_join WHERE ";

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getGroupMembers(array $args, array $keys): bool|object|array
    {
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        foreach ($keys as $key) {
            if (!isset($args[$key])) {
                return false;
            }
        }
        $keyCount = count($keys);
        $sql = "SELECT * FROM group_join WHERE ";

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
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function getGroupMembers_(string|int|null $group_id = null): bool
    {
        try {
            $sql_string = "SELECT `user`.`username` AS `username`,
                           `user`.user_status AS `status`,
                           `user`.user_state AS `state`,
                           `user`.profile_picture AS `profile_picture`,
                           `group_join`.role AS `role`
                            FROM `user`
                            INNER JOIN
                           `group_join` ON
                           `group_join`.`group_id` = :group_id AND 
                           `user`.`id` = `group_join`.`member_id`";
            $this->crud_util->execute($sql_string, ["group_id" => $group_id]);
            if (!$this->crud_util->hasErrors()) {
                $this->group_member_data = $this->crud_util->getResults();
                return true;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupProgress(string|int|null $group_id): float|bool|int
    {
        if($group_id == null) {
            return false;
        }
        try {
            $sql_string = "
                   SELECT 
                   COUNT(CASE WHEN `task`.`status` = 'DONE' THEN 1 END) AS `no_of_completed_tasks`,
                   COUNT(CASE WHEN `task`.`task_id` THEN 1 END) AS `no_of_tasks`
                   FROM 
                     `group_task`
                   JOIN `task` ON `group_task`.`task_id` = `task`.`task_id`
                   WHERE `group_task`.`group_id` = :group_id;
                   ";
            $this->crud_util->execute($sql_string, ["group_id" => $group_id]);
            if (!$this->crud_util->hasErrors()) {
                return $this->crud_util->getFirstResult()->no_of_tasks ?
                    ($this->crud_util->getFirstResult()->no_of_completed_tasks / $this->crud_util->getFirstResult()->no_of_tasks) * 100 :
                    0;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function getGroupStatistics(string|int|null $group_id): bool
    {
        if($group_id == null) {
            return false;
        }
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
                     `group_task`
                   JOIN `task` `t` ON `group_task`.`task_id` = `t`.`task_id`
                   WHERE `group_task`.`group_id` = :group_id;
                   ";
        $stat_per_week_sql_string = "
                    SELECT
                    CONCAT(WEEK(`c_t`.`date`)  - WEEK(`g`.`start_date`) + 1) AS `week`,
                    COUNT(*) AS `no_of_completed_tasks`
                    FROM `completedtask` `c_t`
                    INNER JOIN `group_task` `g_t` ON `c_t`.task_id = `g_t`.task_id
                    INNER JOIN `task` `t` ON `t`.task_id = `c_t`.task_id
                    INNER JOIN `groups` `g` ON `g_t`.group_id = `g`.id
                    WHERE `t`.status = 'DONE'
                      AND `g_t`.`group_id` = :group_id
                      AND `t`.`task_type` = 'group'
                    GROUP BY `week`
                    ORDER BY WEEK(`c_t`.`date`)   - WEEK(`g`.`start_date`) + 1;
        ";
        $this->crud_util->execute($task_details_sql_string, ["group_id" => $group_id]);
        if (!$this->crud_util->hasErrors()) {
            $this->group_data["task_details"] = $this->crud_util->getFirstResult();
            $this->crud_util->execute($stat_per_week_sql_string, ["group_id" => $group_id]);
            if (!$this->crud_util->hasErrors()) {
                $this->group_data["stat_per_week_details"] = $this->crud_util->getResults();
                return true;
            }
        }
        return false;
    }

    public function getPDFData(string|int $group_id): bool
    {
        try {
//             SELECT p.`created_by` AS `creator`, p.`project_name` AS `project_name`, p.`description` AS `description`,
//                    p.`field` AS `project_field`, p.`start_on` AS `start_date`, p.`end_on` AS `end_date`,
//                    p.`created_on` AS `created_on` FROM project p WHERE p.`id` = :project_id;

//             SELECT t.`task_name` AS `name`, t.`description` AS `description`, t.`status` AS `status`, t.`task_type`
//                    AS `type`, t_c.`date` AS `completed_date`, t_c.`time` AS `completed_time` FROM task t JOIN
//             completedtask t_c ON t_c.`task_id` = t.`task_id` WHERE t.`project_id` = :project_id;

            $group_data_sql_string = "SELECT
                    g.`group_name` AS `group_name`,
                    g.`description` AS `group_description`,
                    DATE(g.`start_date`) AS `created_date`,
                    TIME(g.`start_date`) AS `created_time`,
                    CONCAT(u.`first_name`, ' ', u.`last_name`) AS `group_leader`,
                    u.`position` AS `group_leader_position`
                    FROM `groups` g
                    JOIN user u
                    ON g.`leader_id` = u.`id`
                    WHERE g.`id` = :group_id";

            $task_data_sql_string = "SELECT
                    t.`task_name` AS `task_name`,
                    t.`description` AS `task_description`,
                    t.`status` AS `task_status`,
                    t.`task_type` AS `task_type`,
                    t_c.`date` AS `task_completed_date`,
                    t_c.`time` AS `task_completed_time`
                    FROM group_task g_t
                    JOIN completedtask t_c
                    ON t_c.`task_id` = g_t.`task_id`
                    JOIN task t
                    ON g_t.`task_id` = t.`task_id` AND t.task_type = 'group'
                    WHERE g_t.`group_id` = :group_id";

            $data = [];
            $this->crud_util->execute($group_data_sql_string, ["group_id" => $group_id]);

            if (!$this->crud_util->hasErrors()) {

                $data["group_data"] = (array) $this->crud_util->getFirstResult();
                $this->crud_util->execute($task_data_sql_string, ["group_id" => $group_id]);

                if (!$this->crud_util->hasErrors()) {

                    $data["task_data"] = $this->crud_util->getResults();

                    foreach ($data["task_data"] as $key => $task) {
                        $data["task_data"][$key] = (array) $task;
                    }

                    $this->group_data = $data;
                    return true;

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupData(): object|array|null
    {
        return $this->group_data;
    }

    public function getGroupMemberData(): object|array|null
    {
        return $this->group_member_data;
    }
}