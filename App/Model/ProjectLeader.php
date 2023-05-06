<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class ProjectLeader
{

    private CrudUtil $crud_util;
    private array|object $project_data;
    private array|object $project_member_data;

    public function __construct(string|int $project_id)
    {
        try {
            $this->crud_util = new CrudUtil();
            // CAUTION if the project is not found an exception is thrown
            if (!$this->readProjectData($project_id)) {
                throw new \Exception("Project cannot be found");
            } // otherwise the project details can be viewed using project_data
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function readProjectData(string|int $project_id): bool
    {
        try {
            $sql_string = "SELECT * FROM `project` WHERE `id` = :id";
            $args = array("id" => $project_id);
            // execute the query
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                $this->project_data = $result->getFirstResult(); // get all the results or just one result this is an array of objects
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectMembersByRole(string|int $project_id, string $role): bool
    {
       if ($project_id && $role) {
           try {
               // get the details of the clients
               $sql_string = "SELECT u.`id` AS id, u.username AS username, u.profile_picture AS profile_picture, p_j.role AS role FROM project_join p_j JOIN user u ON p_j.member_id = u.id WHERE p_j.project_id = :project_id AND p_j.role = :role";
               $this->crud_util = $this->crud_util->execute($sql_string, ["project_id" => $project_id, "role" => $role]);
               if(!$this->crud_util->hasErrors()) {
                   $this->project_data = $this->crud_util->getResults();
                   return true;
               } else {
                   return false;
               }
           } catch (\Exception $exception) {
               throw  $exception;
           }
       }
       return false;
    }

    public function getProjectMembers(): bool
    {
        try {
            $sql_string = "SELECT `user`.`username` AS `username`,
                           `user`.user_status AS `status`,
                           `user`.user_state AS `state`,
                           `user`.profile_picture AS `profile_picture`,
                           `project_join`.role AS `role`
                            FROM `user`
                            INNER JOIN
                           `project_join` ON
                           `project_join`.`project_id` = :project_id AND 
                           `user`.`id` = `project_join`.`member_id`";
            $this->crud_util->execute($sql_string, ["project_id" => $this->project_data->id]);
            if (!$this->crud_util->hasErrors()) {
                $this->project_member_data = $this->crud_util->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectMemberData(): array|null|object|bool
    {
        return $this->project_member_data;
    }

    public function getProjectData(): object|array|null|bool
    {
        return $this->project_data;
    }
}
