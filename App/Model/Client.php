<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;
use Exception;

class Client implements Model
{
    private CrudUtil $crud_util;
    private object|array|int $project_data;
    private object|array $message_data;

    public function __construct(string|int $project_id = null)
    {
        try {
            $this->crud_util = new CrudUtil();
            // CAUTION if the project is not found an exception is thrown
            if (!$this->readProjectData($project_id)) {
                throw new \Exception("User cannot be found");
            } // otherwise the project details can be viewed using project_data
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function saveProjectFeedbackMessage(string|int $id, string $msg): bool
    {
        // add the message to the project feedback table as well as the messages table
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "PROJECT_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `project_feedback_message`(`message_id`, `project_id`) VALUES (:message_id, :project_id)";
                        $this->crud_util->execute($sql_string, array(
                            "message_id" => $message->id, "project_id" => $this->project_data->id
                        ));
                        if (!$this->crud_util->hasErrors()) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
            // then add the message to the project messages table
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectFeedbackMessages(): bool
    {
        // get all the messages in the project feedback 
        // get all the messages in a project forum
        try {
            // use a join between the messages table and the project table where ids are equal
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_feedback_message` WHERE `project_id` = :project_id)";
                $sql_string  = "SELECT m.*, u.`username` AS `sender_username`, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `project_feedback_message` pfm ON m.`id` = pfm.`message_id` WHERE pfm.`project_id` = :project_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $this->project_data->id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getReportData()
    {
        try {
            throw new \Exception("Not implemented yet");
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
                $sql_string = "SELECT u.id AS id, u.username AS username, u.profile_picture AS profile_picture, p_j.role AS role FROM project_join p_j JOIN user u ON p_j.member_id = u.id WHERE p_j.project_id = :project_id AND p_j.role = :role";
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

    public function getPDFData(string|int $project_id): bool
    {
        try {
//             SELECT p.`created_by` AS `creator`, p.`project_name` AS `project_name`, p.`description` AS `description`,
//                    p.`field` AS `project_field`, p.`start_on` AS `start_date`, p.`end_on` AS `end_date`,
//                    p.`created_on` AS `created_on` FROM project p WHERE p.`id` = :project_id;

//             SELECT t.`task_name` AS `name`, t.`description` AS `description`, t.`status` AS `status`, t.`task_type`
//                    AS `type`, t_c.`date` AS `completed_date`, t_c.`time` AS `completed_time` FROM task t JOIN
//             completedtask t_c ON t_c.`task_id` = t.`task_id` WHERE t.`project_id` = :project_id;

            $project_data_sql_string = "SELECT
                    p.`project_name` AS `project_name`,
                    p.`description` AS `project_description`,
                    p.`field` AS `project_field`,
                    DATE(p.`start_on`) AS `start_date`,
                    TIME(p.`start_on`) AS `start_time`,
                    DATE(p.`end_on`) AS `end_date`,
                    TIME(p.`end_on`) AS `end_time`,
                    p.`created_on` AS `created_on`,
                    CONCAT(u.`first_name`, ' ', u.`last_name`) AS `project_creator`,
                    u.`position` AS `project_creator_position`
                    FROM project p
                    JOIN user u
                    ON p.`created_by` = u.`id`
                    WHERE p.`id` = :project_id";

            $task_data_sql_string = "SELECT
                    t.`task_name` AS `task_name`,
                    t.`description` AS `task_description`,
                    t.`status` AS `task_status`,
                    t.`task_type` AS `task_type`,
                    t_c.`date` AS `task_completed_date`,
                    t_c.`time` AS `task_completed_time`
                    FROM task t
                    JOIN completedtask t_c
                    ON t_c.`task_id` = t.`task_id`
                    WHERE t.`project_id` = :project_id";

            $data = [];
            $this->crud_util->execute($project_data_sql_string, ["project_id" => $project_id]);

            if (!$this->crud_util->hasErrors()) {

                $data["project_data"] = (array) $this->crud_util->getFirstResult();
                $this->crud_util->execute($task_data_sql_string, ["project_id" => $project_id]);

                if (!$this->crud_util->hasErrors()) {

                    $data["task_data"] = $this->crud_util->getResults();

                    foreach ($data["task_data"] as $key => $task) {
                        $data["task_data"][$key] = (array) $task;
                    }

                    $this->project_data = $data;
                    return true;

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectData(): array|object|null
    {
        return $this->project_data;
    }

    public function getMessageData(): array|object|null
    {
        return $this->message_data;
    }
}
