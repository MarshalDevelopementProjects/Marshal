<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Admin implements Model
{

    private $crud_util;
    private $admin_data;
    private $query_results;

    public function __construct(string|int $id = null)
    {
        try {
            $this->crud_util = new CrudUtil();
            if ($id != null) {
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
    }

    // create more than one user in one go
    public function createUsers(array $args = array())
    {
    }

    // update a single user
    public function updateUser(string|int $userID, array $params = array())
    {
    }

    // read a single user in the system 
    public function readUser(string|int $keys)
    {
    }

    // read all the users in the system 
    public function readAllUsers()
    {
    }

    // this function will be used for searching table with conditions
    // mainly operators and all greater than some values and so on
    // mainly used for filtering
    public function conditionalSearch(string $table, string|int|array $fields, string|array $operators)
    {
    }

    // delete a particular user
    public function deleteUser(string|int|array $params)
    {
    }

    // delete all the users that fall under a certain condition
    public function conditionalDelete(string|int|array $params)
    {
    }

    // update a single user using the user id
    public function update(string|int $id, array $args = array())
    {
    }

    // update a single user using the user id
    public function conditionalUpdate(string|int $id, array $args = array())
    {
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
