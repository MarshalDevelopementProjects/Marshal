<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class GroupLeader implements Model
{
    private CrudUtil $crud_util;
    private object|array $group_data;
    private object|array $message_data;

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

    public function saveGroupMessage(string|int $id, string|int $project_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "GROUP_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_message`(`message_id`, `project_id` , `group_id`) VALUES (:message_id, :project_id, :group_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $this->group_data->id));
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

    public function saveGroupFeedbackMessage(string|int $id, string|int $project_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "GROUP_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_feedback_message`(`message_id`, `project_id` , `group_id`) VALUES (:message_id, :project_id, :group_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $this->group_data->id));
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

    public function getGroupFeedbackMessages(string|int $project_id): bool
    {
        // get all the messages in a project forum
        try {
            // use a join between the messages table and the project table where ids are equal
            try {
                $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_feedback_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id, "group_id" => $this->group_data->id));
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

    public function getGroupMessages(string|int $project_id): bool
    {
        try {
            $sql_string = "SELECT * FROM `message` WHERE `id` IN (SELECT `message_id` FROM `group_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
            $args = array("project_id" => $project_id, "group_id" => $this->group_data->id);
            // execute the query
            $result = $this->crud_util->execute($sql_string, $args);
            if ($result->getCount() > 0) {
                $this->message_data = $result->getResults(); // get all the results or just one result this is an array of objects
                return true;
            } else {
                return false;
            }
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

    public function getGroupData()
    {
        return $this->group_data;
    }

    public function getMessageData() {
        return $this->message_data;
    }
}
