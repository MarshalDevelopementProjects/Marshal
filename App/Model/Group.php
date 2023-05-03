<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;
use Exception;

class Group implements Model
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
            return $sql;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getAllGroups(array $args, array $keys): object|bool|array
    {
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
            throw $th;
        }
    }

    public function getGroup(array $args, array $keys): object|bool|array
    {
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
            throw $th;
        }
    }

    public function addGroupMember(array $args, array $keys)
    {
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
            return $sql;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupMember(array $args, array $keys): bool|object|array
    {
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
            throw $th;
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

    public function getGroupProgress(string|int $group_id): float|bool|int
    {
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
            var_dump($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function getGroupStatistics(string|int $group_id): bool
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
                     `group_task`
                   JOIN `task` `t` ON `group_task`.`task_id` = `t`.`task_id`
                   WHERE `group_task`.`group_id` = :group_id;
                   ";
        $stat_per_week_sql_string = "
                    SELECT
                    CONCAT(WEEK(`c_t`.`date`)  - WEEK(`p`.`start_on`) + 1) AS `week`,
                    COUNT(*) AS `no_of_completed_tasks`
                    FROM `completedtask` `c_t`
                    INNER JOIN `group_task` `g_t` ON `c_t`.task_id = `g_t`.task_id
                    INNER JOIN `task` `t` ON `c_t`.task_id = `g_t`.task_id
                    INNER JOIN `project` `p` ON `t`.project_id = `p`.id
                    WHERE `t`.status = 'DONE'
                      AND `g_t`.`group_id` = :group_id
                      AND `t`.`task_type` = 'group'
                      AND `c_t`.`date` < `p`.`end_on`
                    GROUP BY `week`
                    ORDER BY WEEK(`c_t`.`date`)   - WEEK(`p`.`start_on`) + 1;
        ";
        $this->crud_util->execute($task_details_sql_string, ["group_id" => $group_id]);
        if (!$this->crud_util->hasErrors()) {
            $this->group_data["task_details"] = $this->crud_util->getFirstResult();
            $this->crud_util->execute($stat_per_week_sql_string, ["group_id" => $group_id]);
            if (!$this->crud_util->hasErrors()) {
                $this->group_data["stat_per_week_details"] = $this->crud_util->getFirstResult();
                return true;
            }
        }
        return false;
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