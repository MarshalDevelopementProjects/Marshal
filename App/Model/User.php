<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class User implements Model
{
    private $crud_util = null, $user_data = null;

    public function __construct(string|int $id = null)
    {
        try {
            $this->crud_util = new CrudUtil();
            if ($id != null) {
                // CAUTION if the user is not found an exception is thrown
                if (!$this->readUser(key: "id", value: $id)) {
                    throw new \Exception("User cannot be found");
                } // otherwise the user details can be viewed from user_data
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createUser(array $args = array())
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO `user`(`username`, `first_name`, `last_name`, `email_address`, `password`, `phone_number`)
                           VALUES (:username, :first_name, :last_name, :email_address, :password, :phone_number)";
            $args['password'] = password_hash($args['password'], PASSWORD_ARGON2ID);
            try {
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    public function updateState(string|int $id, string $state = "OFFLINE")
    {
        try {
            $sql_string = "UPDATE `user` SET `state` = :state WHERE `id` = :id";
            $this->crud_util->execute($sql_string, ["state" => $state, "id" => $id]);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function readUser(string $key, string|int $value = null)
    {
        // example format => "SELECT * FROM users WHERE id = :id";
        $sql_string = "SELECT * FROM `user` WHERE `" . $key . "` = :" . $key;
        try {
            $result = $this->crud_util->execute($sql_string, array($key => $value));
            if ($result->getCount() > 0) {
                $this->user_data = $result->getFirstResult();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updateUser(string|int $id, array $args = array())
    {
        try {
            $sql_string = "UPDATE `user` SET
                          `username` = :username,
                          `first_name` = :first_name,
                          `last_name` = :last_name,
                          `email_address` = :email_address,
                          `phone_number` = :phone_number,
                          `position` = :position,
                          `bio` = :bio,
                          `user_status` = :user_status
                          WHERE `id` = :id";
            $args["id"] = $id;
            echo "<pre>";
            var_dump($sql_string);
            var_dump($args);
            echo "</pre>";
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // handle with care
    public function delete(string $id = null)
    {
        throw new \Exception("Not implemented");
    }

    public function getUserData()
    {
        return $this->user_data;
    }
}
