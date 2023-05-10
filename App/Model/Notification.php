<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Notification
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

    public function create(array $args, array $keys, string $table): bool
    {
        $keyCount = count($keys);

        $sql = "INSERT INTO " . $table . " (";

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
        // var_dump($sql);
        // var_dump($args);
        try {
            $this->crud_util->execute($sql, $args);
            return true;
        } catch (\Exception $exception) {
            return false;
            // throw $exception;
        }
    }

    public function createNotification(array $args, array $keys):bool
    {
        try {
            $this->create($args, $keys, 'notifications');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function addTaskRefference(array $args, array $keys):bool{
        try {
            $this->create($args, $keys, 'task_notification');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function addMessageRefference(array $args, array $keys):bool{
        try {
            $this->create($args, $keys, 'message_notification');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function setNotifiers(array $args, array $keys):bool{

        try {
            $this->create($args, $keys, 'notification_recievers');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getNotification(array $args, array $keys): object|bool|array
    {
        $keyCount = count($keys);

        $sql = "SELECT * FROM notifications WHERE ";
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

    public function getNotifications($condition){
        $sql  = "SELECT * FROM notifications " . $condition;

        try {
            $result = $this->crud_util->execute($sql);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deleteNotification(string|null $condition, string $table){
        if(!$condition){
            return false;
        }else{
            $sql = "DELETE FROM " .$table . " " . $condition;

            try {
                $this->crud_util->execute($sql);
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        }
        
    }

    public function readNotification(array $args = array()){

        if(array_key_exists('notification_id', $args)) {
            if(!is_int($args['notification_id']) || $args['notification_id'] < 0){
                return false;
            }
        }

        $sql_string = "UPDATE notification_recievers
        SET isRead = 1
        WHERE notification_id = :notification_id AND member_id = :member_id";

        try {
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
