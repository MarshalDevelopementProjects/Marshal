<?php

namespace App\Model;

use App\CrudUtil\CrudUtil;
use \Exception;

class Conference
{
    private CrudUtil $crud_util;
    private array|object|null $data = null;

    public function __construct()
    {
        try{
            $this->crud_util = new CrudUtil();
        } catch(Exception $exception) {
            // TODO: HANDLE THESE EXCEPTIONS IN A GOOD WAY
            throw $exception;
        }
    }

    // TODO: Add the newly scheduled meeting to the notification table as well
    public function scheduleConference(array $args): bool
    {
        if (!empty($args)) {
            try {
                $sql_string = "INSERT INTO `conference`(
                         `conf_name`,
                         `conf_description`,
                         `project_id`,
                         `leader_id`,
                         `client_id`,
                         `on`,
                         `at`)
                          VALUES (:conf_name, :conf_description, :project_id, :leader_id, :client_id, :on, :at)";
                $this->crud_util->execute($sql_string, $args);
                if(!$this->crud_util->hasErrors()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            return false;
        }
    }

    // TODO: ONLY FOR THE CLIENT AND THE PROJECT LEADER
    // TODO: PERHAPS EXTEND FOR GROUP MEMBERS AS WELL
    public function getScheduledConferences(string|int $id, string $role, string $with = ""): bool
    {
        try{
            if ($role === "LEADER" ) {
                $role = "leader_id";
                $with = "client_id";
            } else if ($role === "CLIENT") {
                $role = "client_id";
                $with = "leader_id";
            } else {
                return false;
            }
            /*
             * Example query string :
               SELECT
                    u.`profile_picture` AS `caller_dp`,
                    u.`username` AS `caller_username`,
                    p.`project_name`AS `project_name`,
                    c.`on` AS `scheduled_date`,
                    c.`at` AS `scheduled_time`,
                    c.`status` AS `meeting_status`
               FROM `conference`c
               JOIN `user` u ON c.`leader_id` = u.`id`
               JOIN `project` p ON p.`id` = c.`project_id`
               WHERE c.`leader_id` = 1;
            */
            $sql_string = "SELECT
                                c.`conf_id` AS `conf_id`,
                                u.`profile_picture` AS `caller_dp`,
                                u.`username` AS `caller_username`,
                                p.`project_name`AS `project_name`,
                                c.`on` AS `scheduled_date`,
                                c.`conf_name` AS `scheduled_name`,
                                c.`conf_description` AS `scheduled_description`,
                                c.`at` AS `scheduled_time`,
                                c.`status` AS `meeting_status`
                           FROM `conference` c
                               JOIN `user` u ON c.`" . $with . "` = u.`id`
                               JOIN `project` p ON p.`id` = c.`project_id`
                               WHERE c.`" . $role . "` = :id";
            $this->crud_util = $this->crud_util->execute($sql_string, array("id" => $id));
            if (!$this->crud_util->hasErrors()) {
               $this->data = $this->crud_util->getResults();
               return true;
            } else {
                $this->data = NULL;
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getDetailsOfConference(string|int $conf_id): array|bool
    {
        if ($conf_id) {
            $sql_string = "SELECT * FROM `conference` WHERE `conf_id` = :id";
            try {
                $this->crud_util = $this->crud_util->execute($sql_string, array("id" => $conf_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->data = $this->crud_util->getFirstResult();
                    return true;
                } else {
                   return false;
                }
            } catch (Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function getScheduledConferencesByProject(int|string $id, int|string $project_id, string $role, string $with = ""): bool|array
    {
        try{
            if ($role === "LEADER" ) {
                $role = "leader_id";
                $with = "client_id";
            } else if ($role === "CLIENT") {
                $role = "client_id";
                $with = "leader_id";
            } else {
                return false;
            }
            $sql_string = "SELECT
                                c.`conf_id` AS `conf_id`,
                                u.`profile_picture` AS `caller_dp`,
                                u.`username` AS `caller_username`,
                                p.`project_name`AS `project_name`,
                                c.`conf_name` AS `scheduled_name`,
                                c.`conf_description` AS `scheduled_description`,
                                c.`on` AS `scheduled_date`,
                                c.`at` AS `scheduled_time`,
                                c.`status` AS `meeting_status`
                           FROM `conference` c
                               JOIN `user` u ON c.`" . $with . "` = u.`id`
                               JOIN `project` p ON p.`id` = c.`project_id`
                               WHERE c.`" . $role . "` = :id AND c.`project_id` = " . ":project_id";
            $this->crud_util = $this->crud_util->execute($sql_string, array("id" => $id, "project_id" => $project_id));
            if (!$this->crud_util->hasErrors()) {
                $this->data = $this->crud_util->getResults();
                return true;
            } else {
                $this->data = NULL;
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function changeStatusOfConference(string|int $conf_id, string $status):bool|array
    {
        if ($conf_id && ($status === "PENDING" || $status === "DONE" || $status === "OVERDUE" || $status === "CANCELLED")) {
           try {
               if ($this->getDetailsOfConference($conf_id) && $this->data->status !== "CANCELLED") {
                   $sql_string = "UPDATE `conference` SET `status` = :status WHERE `conf_id` = :conf_id";
                   $this->crud_util->execute($sql_string, array(
                       "status" => $status,
                       "conf_id" => $conf_id
                   ));
                   if (!$this->crud_util->hasErrors()) {
                       return true;
                   }
               } else {
                   return ["Status of a cancelled conference that was scheduled prior can't be changed"];
               }
               return false;
           } catch (Exception $exception) {
               throw $exception;
           }
        }
        return false;
    }

   public function getConferenceData(): array|object|null
    {
        return $this->data;
    }
}