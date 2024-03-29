<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Message
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
    public function sendMessage(array $args, array $keys): bool
    {

        foreach ($keys as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }

        $keyCount = count($keys);

        $sql = "INSERT INTO `message` (";

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

    public function setMessageType(array $args, array $keys, string $table): bool
    {
        foreach ($keys as $key) {
            if (!isset($args[$key]) || empty($args[$key])) {
                return false;
            }
        }
        $keyCount = count($keys);

        $sql = "INSERT INTO `" . $table . "` (";

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
    public function getMessage($args, $keys): object|array|bool
    {
        $keyCount = count($keys);

        $sql = "SELECT * FROM `message` WHERE ";
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
            throw $exception;
        }
    }

    public function getMessages($condition):object|array|bool
    {
        $sql = "SELECT * FROM `message` WHERE ";
        $sql .= $condition;

        try {
            $result = $this->crud_util->execute($sql);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Exception $exception) {
            return array();
        }
    }

    public function getAnnouncementHeading($condition, $table):object|array|bool
    {
        $sql = "SELECT heading FROM " . $table . " WHERE " . $condition;
        try {
            $result = $this->crud_util->execute($sql);
            if ($result->getCount() > 0) {
                return $result->getFirstResult();
            } else {
                return array();
            }
        } catch (\Throwable $th) {
            return array();
        }
    }

    public function deleteMessage(string|null $condition, string $table){
        if(!$condition){
            return false;
        }
        $sql = "DELETE FROM " .$table . " " . $condition;

        try {
            $this->crud_util->execute($sql);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
