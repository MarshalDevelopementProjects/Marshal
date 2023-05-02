<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Group implements Model
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
}
