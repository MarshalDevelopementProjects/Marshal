<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class ProjectLeader implements Model
{

    private CrudUtil $crud_util;
    private array|object $project_data;
    private array|object $message_data;

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
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'PROJECT_FEEDBACK_MESSAGE'";
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
        try {
            // use a join between the messages table and the project table where ids are equal
            try {
                $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_feedback_message` WHERE `project_id` = :project_id )";
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

    public function saveForumMessage(string|int $id, string $msg): bool
    {
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "PROJECT_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'PROJECT_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `project_message`(`message_id`, `project_id`) VALUES (:message_id, :project_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $this->project_data->id));
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

    public function getForumMessages(): bool
    {
        // get all the messages in a project forum
        try {
            $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_message` WHERE `project_id` = :project_id)";
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
    }
    // used to give the feedback for the groups
    public function saveGroupFeedbackMessage(string|int $id, string|int $group_id, string $msg): bool
    {
        // add the message to the project message table and the messages table
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "GROUP_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'GROUP_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_feedback_message`(`message_id`, `project_id`, `group_id`) VALUES (:message_id, :project_id, :group_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $this->project_data->id, "group_id" => $group_id));
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

    public function getGroupFeedbackMessages(string|int $group_id): bool
    {
        // get all the messages in a project forum
        try {
            $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_feedback_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
            $this->crud_util->execute($sql_string, array("project_id" => $this->project_data->id, "group_id" => $group_id));
            if (!$this->crud_util->hasErrors()) {
                $this->message_data = $this->crud_util->getResults();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getReportData(string|int $project_id)
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

    public function getProjectData(): object|array|null|bool
    {
        return $this->project_data;
    }

    public function getMessageData(): object|array|null|bool
    {
        return $this->message_data;
    }
}
