<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Notification implements Model
{
    private CrudUtil $crud_util;

    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function createNotification(array $args = array())
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO notifications(`projectId`, `message`, `type`, `senderId`, `sendTime`) VALUES (:projectId, :message, :type, :senderId, :sendTime)";

            try {
                $this->crud_util->execute($sql_string, $args);

                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }
    public function setNotifiedMembers(array $args = array())
    {
        if (!empty($args)) {
            $sql_string = "INSERT INTO notification_recievers(`notificationId`, `memberId`) VALUES (:notificationId, :memberId)";

            try {
                $this->crud_util->execute($sql_string, $args);

                return true;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    public function getNotificationData(array $conditions = array())
    {
        $sql_string = "SELECT * FROM notifications";
        if ($conditions != []) {

            $sql_string .= " WHERE ";
            $keys = array_keys($conditions);

            for ($i = 0; $i < count($keys); $i++) {
                $sql_string .= $keys[$i] . ' = :' . $keys[$i];
                if ($i != count($conditions) - 1) {
                    $sql_string .= ' AND ';
                }
            }
        }

        $result = $this->crud_util->execute($sql_string, $conditions);
        if ($result->getCount() > 0) {
            return $result->getResults();
        } else {
            return false;
        }
    }

    public function getNotificationsOfUser(array $args = array())
    {
        $sql_string = "SELECT * FROM notifications WHERE id IN 
        (SELECT notificationId FROM notification_recievers WHERE memberId = :memberId AND isRead = 0)";

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getResults();
        } else {
            return false;
        }
    }

    public function getNotificationProjectDetails(array $args = array())
    {
        $sql_string = "SELECT * project WHERE id = :id";

        $result = $this->crud_util->execute($sql_string, $args);
        if ($result->getCount() > 0) {
            return $result->getResults();
        } else {
            return false;
        }
    }

    public function readNotification(array $args = array())
    {

        $sql_string = "UPDATE notification_recievers
        SET isRead = 1
        WHERE notificationId = :notificationId AND memberId = :memberId";

        try {
            $this->crud_util->execute($sql_string, $args);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
