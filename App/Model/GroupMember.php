<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class GroupMember
{

    private CrudUtil $crud_util;
    private object|array $group_data;
    private array|object $group_member_data;

    public function __construct(string|int $project_id, string|int $group_id)
    {
        try {
            $this->crud_util = new CrudUtil();
            // CAUTION if the group is not found an exception is thrown
            if (!$this->readGroupData($project_id, $group_id)) {
                throw new \Exception("User cannot be found");
            } // otherwise the group details can be viewed using group_data
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function readGroupData(string|int $project_id, string|int $group_id): bool
    {
        try {
            $sql_string = "SELECT * FROM `groups` WHERE `project_id` = :project_id AND `id` = :id";
            $args = array("project_id" => $project_id, "id" => $group_id);
            // execute the query
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                $this->group_data = $result->getFirstResult(); // get all the results or just one result this is an array of objects
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupMembers(): bool
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
            $this->crud_util->execute($sql_string, ["group_id" => $this->group_data->id]);
            if (!$this->crud_util->hasErrors()) {
                $this->group_member_data = $this->crud_util->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getGroupMemberData(): array|null|object|bool
    {
        return $this->group_member_data;
    }

    public function getGroupData(): object|array
    {
        return $this->group_data;
    }
}
