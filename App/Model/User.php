<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class User
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

    public function createUser(array $args = array()): bool
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO `user`(`username`, `first_name`, `last_name`, `email_address`, `password`, `verification_code`)
                           VALUES (:username, :first_name, :last_name, :email_address, :password, :verification_code)";
            if(array_key_exists("password", $args)) $args['password'] = password_hash($args['password'], PASSWORD_ARGON2ID);
            try {
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    public function updateState(string|int $id, string $user_state): bool
    {
        try {
            if($id && $user_state) {
                $sql_string = "UPDATE `user` SET `user_state` = :user_state WHERE `id` = :id";
                $this->crud_util->execute($sql_string, ["user_state" => $user_state, "id" => $id]);
                return true;
            } return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updatePassword(string|int $id, string $new_password): bool
    {
        try {
            if($id && $new_password) {
                $sql_string = "UPDATE `user` SET `password` = :password WHERE `id` = :id";
                $args['id'] = $id;
                $args['password'] = password_hash($new_password, PASSWORD_ARGON2ID);
                $this->crud_util->execute($sql_string, $args);
                return true;
            } return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function updateVerificationCode(string|int $id, int|string $verification_code): bool
    {
        try {
            $sql_string = "UPDATE `user` SET `verification_code` = :verification_code WHERE `id` = :id";
            $args = ["id" => $id, "verification_code" => $verification_code];
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function readUser(string $key, string|int $value): bool
    {
       if($key && $value) {
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
       } return false;
    }

    public function readMember(string $key, string|int $value): object|bool|array|int|null
    {
        // example format => "SELECT * FROM users WHERE id = :id";
        $sql_string = "SELECT * FROM `user` WHERE `" . $key . "` = :" . $key;
        try {
            $result = $this->crud_util->execute($sql_string, array($key => $value));
            if ($result->getCount() > 0) {
                return $result->getFirstResult();
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getAllUsers(array $args, string $condition)
    {

        $sql_string = "SELECT * FROM user " . $condition;

        try {
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getUserProfiles(array $args, string $condition)
    {

        $sql_string = "SELECT profile_picture FROM user " . $condition;

        try {
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateProfilePicture(string|int $id, string $value): bool
    {
       if ($id && $value) {
           $sql_string = "UPDATE `user` SET `profile_picture` = :profile_picture WHERE `id` =:id";
           try {
               $this->crud_util->execute($sql_string, array("id" => $id, "profile_picture" => $value));
               return true;
           } catch (\Exception $exception) {
               throw $exception;
           }
       }  return false;
    }

    public function updateUser(string|int $id, array $args): bool
    {
        if ($id && $args) {
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
        } return false;
    }

    public function updateVerified(string|int $id, string $value): bool
    {
        if ($id && $value) {
            try {
                $sql_string = "UPDATE `user` SET
                              `verified` = :verified
                              WHERE `id` = :id";
                $args = ["id" => $id, "verified" => $value];
                $this->crud_util->execute($sql_string, $args);
                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function checkUserRole(string|int $req_id, string $role, string $type): bool
    {
        if ($req_id && $role && $type) {
            try {
                $sql_string = "SELECT `member_id` FROM ";
                if ($type === "PROJECT") {
                    if ($role == "LEADER") {
                        $sql_string .= "`project_join` WHERE `member_id` = :user_id AND `project_id` = :req_id AND `role` = 'LEADER'";
                    } else if ($role === "CLIENT") {
                        $sql_string .= "`project_join` WHERE `member_id` = :user_id AND `project_id` = :req_id AND `role` = 'CLIENT'";
                    } else if ($role === "MEMBER") {
                        $sql_string .= "`project_join` WHERE `member_id` = :user_id AND `project_id` = :req_id AND `role` != 'CLIENT'";
                    } else {
                        return false;
                    }
                } else if ($type === "GROUP") {
                    if ($role === "LEADER")
                        $sql_string .= "`group_join` WHERE  `member_id` = :user_id AND `group_id` = :req_id AND `role` = 'LEADER'";
                    else if ($role === "MEMBER")
                        $sql_string .= "`group_join` WHERE  `member_id` = :user_id AND `group_id` = :req_id AND `role` != 'CLIENT'";
                    else
                        return false;
                } else {
                    return false;
                }
                $result = $this->crud_util->execute($sql_string, ["user_id" => $this->user_data->id, "req_id" => $req_id]);
                // var_dump($result);
                if ($result->getCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function getUserData()
    {
        return $this->user_data;
    }

    public function isUserJoinedToProject(array $args = array())
    {
        $sql_string = "SELECT * FROM project_join WHERE project_id = :project_id AND member_id = :member_id";
        try {
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getCommit(string|int $id)
    {
        $sql_string = "SELECT *
        FROM `completedtask`
        WHERE `task_id` IN (
          SELECT `task_id`
          FROM `task`
          WHERE `member_id` = $id AND `status` = 'REVIEW'
        )";
        try {
            $result = $this->crud_util->execute($sql_string);
            if ($result->getCount() > 0) {
                return $result->getResults();
                
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
