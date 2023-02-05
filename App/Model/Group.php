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

    public function createGroup(array $args, array $keys){
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

    public function getAllGroups(array $args, array $keys){
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
}