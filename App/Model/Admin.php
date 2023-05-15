<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Admin
{
    private CrudUtil $crud_util;
    private object|array|null|bool $admin_data = null;
    private object|array|null|bool $query_results = null;

    public function __construct(private string $id = "")
    {
        try {
            $this->crud_util = new CrudUtil();
            if ($id != "") {
                if (!$this->readAdmin(key: "id", value: $id)) {
                    throw new \Exception("Admin cannot be found");
                }
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // this will only be used for testing since admins aren't allowed
    // create other administrators
    public function createAdmin(array $args = array())
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO `admin` (`username`, `first_name`, `last_name`, `email_address`, `password`, `street_address`, `city`, `country`, `phone_number`)
                           VALUES (:username, :first_name, :last_name, :email_address, :password, :street_address, :city, :country, :phone_number)";
            if(array_key_exists("password", $args)) $args['password'] = password_hash($args['password'], PASSWORD_ARGON2ID);
            else return false;
            try {
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    // used to read administrator data a single user
    // this only performs a read by field
    public function readAdmin(string $key, string|int $value): bool
    {
        if ($key && $value) {
            $sql_string = "SELECT * FROM `admin` WHERE `" . $key . "` = :" . $key;
            // example format => "SELECT * FROM users WHERE id = :id";
            try {
                $result = $this->crud_util->execute($sql_string, array($key => $value));
                if ($result->getCount() > 0) {
                    $this->admin_data = $result->getFirstResult();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    // create a single user
    public function createUser(array $args = array()): bool
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO `user`(`username`, `first_name`, `last_name`, `email_address`, `password`, `access`, `verified`)
                           VALUES (:username, :first_name, :last_name, :email_address, :password, :access, :verified)";
            if(array_key_exists("password", $args)) $args['password'] = password_hash($args['password'], PASSWORD_ARGON2ID);
            else return false;
            $args["access"] = "ENABLED";
            $args["verified"] = "TRUE";
            try {
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

/*    // create more than one user in one go
    public function createUsers(array $args = array())
    {
        foreach ($args as $user) {
            $val = true;
            if (!empty($user)) {
                try {
                   $val = $this->createUser($user);
                } catch (\Exception $exception) {
                    throw $exception;
                }
            } else {
                return false;
            }
            if (!$val) return $val;
        }
        return true;
    }*/

  /*  // update a single user
    public function updateUserDetails(string|int $id, array $args = array())
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
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }*/

    // read a single user in the system 
    public function readUser(string $value ,string|int $key = "username"): bool
    {
        if ($key && $value) {
            $sql_string = "SELECT * FROM `user` WHERE `" . $key . "` = :" . $key;
            // example format => "SELECT * FROM users WHERE id = :id";
            try {
                $result = $this->crud_util->execute($sql_string, [$key => $value]);
                if ($result->getCount() > 0) {
                    $this->query_results = $result->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    // read all the users in the system 
    public function readAllUsers()
    {
        // $sql_string = "SELECT `id`, `username`, `email_address` FROM `user`";
        $sql_string = "SELECT `id`, `username`, `email_address`,`access`,`user_status`,`joined_datetime`,`profile_picture`,`user_state`,`phone_number`,`position`,`first_name`,`last_name` FROM `user`";

        try {
            $result = $this->crud_util->execute($sql_string);
            if ($result->getCount() > 0) {
                $this->query_results = $result->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

/*    // this function will be used for searching table with conditions
    // mainly operators and all greater than some values and so on
    // mainly used for filtering
    public function conditionalSearch(string $table = "user", string|int|array $fields, string|array $operators)
    {
    }*/

    public function getActiveUsers()
    {
        $sql_string = "SELECT `id`, `username`, `email_address`,`access`,`user_status`,`joined_datetime`,`profile_picture`,`user_state` FROM `user` WHERE `user_state` = 'ONLINE'";
        try {
            $result = $this->crud_util->execute($sql_string);
            if ($result->getCount() > 0) {
                $this->query_results = $result->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getOfflineUsers()
    {
        $sql_string = "SELECT `id`, `username`, `email_address`,`access`,`user_status`,`joined_datetime`,`profile_picture`,`user_state` FROM `user` WHERE `user_state` = 'OFFLINE'";
        try {
            $result = $this->crud_util->execute($sql_string);
            if ($result->getCount() > 0) {
                $this->query_results = $result->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getBlockedUsers()
    {
        $sql_string = "SELECT `id`, `username`, `email_address`,`access`,`user_status`,`joined_datetime`,`profile_picture` FROM `user` WHERE `access` = 'DISABLED'";
        try {
            $result = $this->crud_util->execute($sql_string);
            if ($result->getCount() > 0) {
                $this->query_results = $result->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // disable a particular user account
    public function disableUserAccount(string $key, string|int $value)
    {
       if ($key && $value) {
           try {
               $sql_string = "UPDATE `user` SET
                          `access` = :access
                          WHERE `$key` = :$key";
               $this->crud_util->execute($sql_string, ["access" => "DISABLED", $key => $value]);
               return true;
           } catch (\Exception $exception) {
               throw $exception;
           }
       } return false;
    }

    // enable a particular user account
    public function enableUserAccount(string $key, string|int $value)
    {
        if ($key && $value) {
            try {
                $sql_string = "UPDATE `user` SET
                          `access` = :access
                          WHERE `$key` = :$key";
                $this->crud_util->execute($sql_string, ["access" => "ENABLED", $key => $value]);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    /*// update admin details
    private function updateAdminDetails(string|int $id, array $args = array())
    {
        try {
            $sql_string = "UPDATE `admin` SET
                          `username` = :username,
                          `first_name` = :first_name,
                          `last_name` = :last_name,
                          `email_address` = :email_address,
                          `phone_number` = :phone_number,
                          `street_address` = :street_address,
                          `city` = :city,
                          `bio` = :bio,
                          `country` = country:
                          WHERE `id` = :id";
            $args["id"] = $id;
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }*/

    public function getAdminData(): object|bool|array|null
    {
        return $this->admin_data ?? [];
    }

    public function getQueryResults(): object|bool|array|null
    {
        return $this->query_results ?? [];
    }
}
