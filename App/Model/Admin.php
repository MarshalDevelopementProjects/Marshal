<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Admin implements Model
{
    private $crud_util;
    private $admin_data;
    private $query_results;

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
    private function createAdmin(array $args = array())
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO `admin` (`id`, `username`, `first_name`, `last_name`, `email_address`, `password`, `street_address`, `city`, `country`, `phone_number`)
                           VALUES (:id, :username, :first_name, :last_name, :email_address, :password, :street_address, :city, :country, :phone_number)";
            $args['id'] = uniqid("admin");
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

    // used to read administrator data a single user
    // this only performs a read by field
    public function readAdmin(string $key, string|int $value)
    {
        if ($key) {
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

    // create more than one user in one go
    public function createUsers(array $args = array())
    {
        foreach ($args as $user) {
            if (!empty($user)) {
                $sql_string = "INSERT INTO `user`(`username`, `first_name`, `last_name`, `email_address`, `password`, `phone_number`)
                           VALUES (:username, :first_name, :last_name, :email_address, :password, :phone_number)";
                $user['password'] = password_hash($user['password'], PASSWORD_ARGON2ID);
                try {
                    $this->crud_util->execute($sql_string, $user);
                    return true;
                } catch (\Exception $exception) {
                    throw $exception;
                }
            }
            return false;
        }
    }

    // update a single user
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
    }

    // read a single user in the system 
    public function readUser(string|int $key = "username", string $value)
    {
        if ($key) {
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
        $sql_string = "SELECT `id`, `username`, `email_address` FROM `user`";
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

    // this function will be used for searching table with conditions
    // mainly operators and all greater than some values and so on
    // mainly used for filtering
    public function conditionalSearch(string $table = "user", string|int|array $fields, string|array $operators)
    {
    }

    public function getActiveUsers()
    {
        $sql_string = "SELECT `id`, `username`, `email_address` FROM `user` WHERE `user_state` = 'ONLINE'";
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
        $sql_string = "SELECT `id`, `username`, `email_address` FROM `user` WHERE `access` = 'DISABLED'";
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
        try {
            $sql_string = "UPDATE `user` SET
                          `access` = :access
                          WHERE `$key` = :$key";
            $this->crud_util->execute($sql_string, ["access" => "DISABLED", $key => $value]);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // enable a particular user account
    public function enableUserAccount(string $key, string|int $value)
    {
        try {
            $sql_string = "UPDATE `user` SET
                          `access` = :access
                          WHERE `$key` = :$key";
            $this->crud_util->execute($sql_string, ["access" => "ENABLED", $key => $value]);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // update admin details
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
    }

    public function getAdminData()
    {
        return $this->admin_data;
    }

    public function getQueryResults()
    {
        return $this->query_results;
    }
}
