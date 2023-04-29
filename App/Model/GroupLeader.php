<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class GroupLeader implements Model
{
    private CrudUtil $crud_util;
    private object|array $group_data;
    private object|array|null $message_data;
    private object|array|null $group_member_data;

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

    /*
     * SELECT m.*, u.profile_picture
     * FROM message m
     * JOIN user u ON m.sender_id = u.id
     * JOIN project_message pm ON m.id = pm.message_id
     * WHERE pm.project_id = [YOUR_PROJECT_ID] ORDER BY m.stamp;
     *
     * */

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

    public function getGroupFeedbackForumMessages(string|int $project_id): bool
    {
        // get all the messages in a project forum
        try {
            // use a join between the messages table and the project table where ids are equal
            try {
                // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_feedback_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
                $sql_string  = "SELECT m.*, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_feedback_message` gfm ON m.`id` = gfm.`message_id` WHERE gfm.`project_id` = :project_id  AND gfm.`group_id` = :group_id ORDER BY m.stamp";
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

    public function getGroupForumMessages(string|int $project_id): bool
    {
        try {
            // $sql_string = "SELECT * FROM `message` WHERE `id` IN (SELECT `message_id` FROM `group_message` WHERE `project_id` = :project_id AND `group_id` = :group_id)";
            $sql_string  = "SELECT m.*, u.`username` AS `sender_username`, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_message` gm ON m.`id` = gm.`message_id` WHERE gm.`project_id` = :project_id AND gm.`group_id` = :group_id ORDER BY m.stamp";
            $args = array("project_id" => $project_id, "group_id" => $this->group_data->id);
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


    public function saveGroupTaskFeedbackMessage(string|int $id, string|int $project_id, string|int $task_id, string $msg): bool
    {
        try {
            // add the message to the message table first
            if (!empty($msg)) {
                $sql_string = "INSERT INTO `message`(`sender_id`, `stamp`, `message_type`, `msg`) VALUES (:sender_id, :stamp, :message_type, :msg)";
                $date_time = date('Y-m-d H:i:s');
                $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time, "message_type" => "GROUP_TASK_FEEDBACK_MESSAGE", "msg" => $msg));
                if (!$this->crud_util->hasErrors()) {
                    $sql_string = "SELECT `id` FROM `message` WHERE `sender_id` = :sender_id AND `stamp` = :stamp AND `message_type` = 'GROUP_TASK_FEEDBACK_MESSAGE'";
                    $this->crud_util->execute($sql_string, array("sender_id" => $id, "stamp" => $date_time));
                    if (!$this->crud_util->hasErrors()) {
                        $message = $this->crud_util->getFirstResult();
                        $sql_string = "INSERT INTO `group_task_feedback_message`(`message_id`, `project_id`, `group_id`,`task_id`) VALUES (:message_id, :project_id, :group_id,:task_id)";
                        $this->crud_util->execute($sql_string, array("message_id" => $message->id, "project_id" => $project_id, "group_id" => $this->group_data->id, "task_id" => $task_id));
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

    public function getGroupTaskFeedbackMessages(string|int $project_id, string|int $task_id): bool
    {
        // get all the messages in a project forum
        try {
            // $sql_string = "SELECT * FROM `message` WHERE `id` in (SELECT `message_id` FROM `group_task_feedback_message` WHERE `project_id` = :project_id AND `task_id` = :task_id)";
            $sql_string = "SELECT m.*, u.`profile_picture` AS `sender_profile_picture` FROM `message` m JOIN `user` u ON m.`sender_id` = u.`id` JOIN `group_task_feedback_message` gtfm ON m.`id` = gtfm.`message_id` WHERE gtfm.`project_id` = :project_id AND gtfm.`group_id` = :group_id AND gtfm.`task_id` = :task_id ORDER BY m.stamp";
            $this->crud_util->execute($sql_string, array("project_id" => $project_id, "group_id" => $this->group_data->id, "task_id" => $task_id));
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

    public function getMessageData(): object|array
    {
        return $this->message_data;
    }
}
