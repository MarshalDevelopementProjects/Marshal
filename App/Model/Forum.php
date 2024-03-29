<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;
use Exception;

class Forum
{
    private CrudUtil $crud_util;

    private array|object|null $message_data = null;

    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /*
      *
         SELECT m.*, u.profile_picture FROM message m JOIN user u ON m.sender_id = u.id JOIN project_message pm ON m.id = pm.message_id WHERE pm.project_id = [YOUR_PROJECT_ID] ORDER BY m.stamp;
      *
      * */
    public function saveForumMessage(string|int $sender_id, string|int $project_id, string $msg): bool
    {
        try {
            $this->crud_util->getPDO()->beginTransaction();
            if ($sender_id && $project_id && !empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "PROJECT_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'PROJECT_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `project_message`(`message_id`, `project_id`) VALUES (:message_id, :project_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    public function getForumMessages(string|int $project_id): bool
    {
        // get all the messages in a project forum
        // use a join between the messages table and the project table where ids are equal
        if($project_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_message` WHERE `project_id` = :project_id)";
                $sql_string  = "SELECT m.*, u.`profile_picture` AS `sender_profile_picture` , u.`username` AS `sender_username` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `project_message` pm ON m.`id` = pm.`message_id` WHERE pm.`project_id` = :project_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function saveProjectTaskFeedbackMessage(string|int $sender_id, string|int $project_id,string|int $task_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            $this->crud_util->getPDO()->beginTransaction();
            if ($sender_id && $project_id && $task_id && !empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "TASK_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'TASK_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `project_task_feedback_message`(`message_id`, `project_id`, `task_id`) VALUES (:message_id, :project_id, :task_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "task_id" => $task_id));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
            // then add the message to the project messages table
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    public function getProjectTaskFeedbackMessages(string|int $project_id, string|int $task_id): bool
    {
        // get all the messages in a project forum
        if($project_id && $task_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_task_feedback_message` WHERE `project_id` = :project_id AND `task_id` = :task_id)";
                $sql_string = "SELECT m.*, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `project_task_feedback_message` ptfm ON m.`id` = ptfm.`message_id` WHERE ptfm.`project_id` = :project_id AND ptfm.`task_id` = :task_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id, "task_id" => $task_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function saveGroupFeedbackMessage(string|int $sender_id, string|int $project_id, string|int $group_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            $this->crud_util->getPDO()->beginTransaction();
            if ($sender_id && $group_id && $project_id && !empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "GROUP_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'GROUP_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_feedback_message`(`message_id`, `project_id`, `group_id`) VALUES (:message_id, :project_id, :group_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $group_id));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
            // then add the message to the project messages table
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    // SELECT m.*, u.profile_picture FROM message m JOIN user u ON m.sender_id = u.id JOIN project_message pm ON m.id = pm.message_id WHERE pm.project_id = [YOUR_PROJECT_ID] ORDER BY m.stamp;
    public function getGroupFeedbackMessages(string|int $project_id, string|int $group_id): bool
    {
        // get all the messages in a project forum
        if ($project_id && $group_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_feedback_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
                $sql_string = "SELECT m.*, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_feedback_message` gfm ON m.`id` = gfm.`message_id` WHERE gfm.`project_id` = :project_id  AND gfm.`group_id` = :group_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id, "group_id" => $group_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function saveGroupMessage(string|int $sender_id, string|int $project_id, string|int $group_id, string $msg): bool
    {
        try {
            $this->crud_util->getPDO()->beginTransaction();
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "GROUP_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_message`(`message_id`, `project_id` , `group_id`) VALUES (:message_id, :project_id, :group_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $group_id));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    public function getGroupForumMessages(string|int $project_id, string|int $group_id): bool
    {
        if($project_id && $group_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` IN (SELECT `message_id` FROM `group_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
                $sql_string = "SELECT m.*, u.`username` AS `sender_username`, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_message` gm ON m.`id` = gm.`message_id` WHERE gm.`project_id` = :project_id AND gm.`group_id` = :group_id ORDER BY m.stamp";
                $args = array("project_id" => $project_id, "group_id" => $group_id);
                // execute the query
                $result = $this->crud_util->execute($sql_string, $args);
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $result->getResults(); // get all the results or just one result this is an array of objects
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    /*public function saveGroupTaskFeedbackMessage(string|int $sender_id, string|int $project_id, string|int $group_id, string|int $task_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            $this->crud_util->getPDO()->beginTransaction();
            if ($sender_id && $project_id && $group_id && $task_id && !empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "GROUP_TASK_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'GROUP_TASK_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_task_feedback_message`(`message_id`, `project_id`, `group_id`,`task_id`) VALUES (:message_id, :project_id, :group_id,:task_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $group_id, "task_id" => $task_id));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    public function getGroupTaskFeedbackMessages(string|int $project_id, string|int $group_id ,string|int $task_id): bool
    {
        // get all the messages in a project forum
        if($project_id && $group_id && $task_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_task_feedback_message` WHERE `project_id` = :project_id AND `task_id` = :task_id)";
                $sql_string = "SELECT
                           m.*,
                           u.`profile_picture` AS `sender_profile_picture`
                           FROM `message` m
                               JOIN `user` u
                                   ON m.`sender_id` = u.`id`
                               JOIN `group_task_feedback_message` gtfm
                                   ON m.`id` = gtfm.`message_id`
                           WHERE gtfm.`project_id` = :project_id
                             AND gtfm.`group_id` = :group_id
                             AND gtfm.`task_id` = :task_id
                           ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id, "group_id" => $group_id, "task_id" => $task_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }*/

    public function saveProjectFeedbackMessage(string|int $sender_id, string|int $project_id, string $msg): bool
    {
        // add the message to the project feedback table as well as the messages table
        try {
            // add the message to the message table first
            $this->crud_util->getPDO()->beginTransaction();
            if ($sender_id && $project_id && !empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time, "message_type" => "PROJECT_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'PROJECT_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $sender_id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `project_feedback_message`(`message_id`, `project_id`) VALUES (:message_id, :project_id)";
                        $this->crud_util->execute($sql_string, array(
                            "message_id" => $message->id, "project_id" => $project_id
                        ));
                        if (!$this->crud_util->hasErrors()) {
                            $this->crud_util->getPDO()->commit();
                            return true;
                        }
                    }
                }
            }
            $this->crud_util->getPDO()->rollBack();
            return false;
        } catch (\Exception $exception) {
            $this->crud_util->getPDO()->rollBack();
            throw $exception;
        }
    }

    // SELECT m.*, u.profile_picture FROM message m JOIN user u ON m.sender_id = u.id JOIN project_message pm ON m.id = pm.message_id WHERE pm.project_id = [YOUR_PROJECT_ID] ORDER BY m.stamp;
    public function getProjectFeedbackMessages(string|int $project_id): bool
    {
        if($project_id) {
            // use a join between the messages table and the project table where ids are equal
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `project_feedback_message` WHERE `project_id` = :project_id )";
                $sql_string = "SELECT m.*, u.`username` AS `sender_username`, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `project_feedback_message` pfm ON m.`id` = pfm.`message_id` WHERE pfm.`project_id` = :project_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id));
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
        return false;
    }

    /*
     * SELECT m.*, u.profile_picture
     * FROM message m
     * JOIN user u ON m.sender_id = u.id
     * JOIN project_message pm ON m.id = pm.message_id
     * WHERE pm.project_id = [YOUR_PROJECT_ID] ORDER BY m.stamp;
     *
     * */
    public function getGroupFeedbackForumMessages(string|int $project_id, string|int $group_id): bool
    {
            // use a join between the messages table and the project table where ids are equal
        if($project_id && $group_id) {
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_feedback_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
                $sql_string  = "SELECT m.*, u.`username` AS `sender_username`, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_feedback_message` gfm ON m.`id` = gfm.`message_id` WHERE gfm.`project_id` = :project_id  AND gfm.`group_id` = :group_id ORDER BY m.stamp";
                $this->crud_util->execute($sql_string, array("project_id" => $project_id, "group_id" => $group_id));
                if (!$this->crud_util->hasErrors()) {
                    $this->message_data = $this->crud_util->getResults();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $exception) {
                throw $exception;
            }
        } return false;
    }

    public function getMessageData(): array|object|null|bool
    {
        return $this->message_data ?? [];
    }
}