<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Client implements Model
{
    private CrudUtil $crud_util;
    private object|int $project_data;
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

    public function saveProjectFeedbackMessage(string|int $id, string $msg)
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
                        $message_id = $this->crud_util->getFirstResult()["id"];
                        $sql_string = "INSERT INTO `project_feedback_message`(`message_id`, `project_id`) VALUES (:message_id, :project_id)";
                        $this->crud_util->execute($sql_string, array(
                            "message_id" => $message_id, "project_id" => $this->project_data->id
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

    public function getProjectFeedbackMessages()
    {
        // get all the messages in the project feedback 
        // get all the messages in a project forum
        try {
            // use a join between the messages table and the project table where ids are equal
            try {
                $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_feedback_message` WHERE `project_id` = :project_id)";
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
            $args = array("project_id" => $project_id);
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

    public function getProjectData(): array|object|null
    {
        return $this->project_data;
    }

    public function getMessageData(): array|object|null
    {
        return $this->message_data;
    }
}
