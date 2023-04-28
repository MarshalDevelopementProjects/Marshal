<?php

namespace App\Controller\Notification;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\Controller;
use App\Model\Notification;

class NotificationController{
    
    public function defaultAction(Object|array|string|int $optional = null){
    }

    public function setNotification(array $args):int{

        /* args format is 
                        args = array(
                            message =>
                            type => 
                            senderId =>
                            url =>
                            reciveId =>
                        )
        */

        // var_dump($args);
        $notification = new Notification();
        
        try {
            $date = date("Y-m-d H:i:s");
            $projectId = $_SESSION['project_id'];

            $notificationArgs = array(
                "project_id" => $projectId,
                "message" => $args['message'],
                "type" => $args['type'],
                "sender_id" => $args['sender_id'],
                "send_time" => $date,
                "url" => $args['url']
            );
            $notification->createNotification($notificationArgs, array("project_id", "message", "type", "sender_id", "send_time", "url"));
            
            $notifyConditions = array(
                "project_id" => $projectId,
                "sender_id" => $args['sender_id'],
                "send_time" => $date
            );
            $newNotification = $notification->getNotification($notifyConditions, array("project_id", "sender_id", "send_time"));

            if($args['recive_id']){
                $notifyMemberArgs = array(
                    "notification_id" => $newNotification->id,
                    "member_id" => $args['recive_id']
                );
                $notification->setNotifiers($notifyMemberArgs, array("notification_id", "member_id"));
            }

            return $newNotification->id;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function boardcastNotification(int $notificationId, array $members):bool{

        $notification = new Notification();
        try {
            foreach($members as $member){
                $notification->setNotifiers(array("notification_id" => $notificationId, "member_id" => $member->member_id), array("notification_id", "member_id"));
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}