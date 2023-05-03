<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;
use Exception;

class PDF implements model
{
    private CrudUtil $crud_util;
    private object|array|null $pdf_data;

    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (Exception $exception) {
            throw $exception;
        }
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

                    $this->pdf_data = $data;
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

    public function getData(): array|object|null
    {
        return $this->pdf_data;
    }
}