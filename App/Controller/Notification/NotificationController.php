<?php

namespace App\Controller\Notification;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\Controller;
use App\Model\Notification;

class NotificationController extends Controller{
    
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
                "projectId" => $projectId,
                "message" => $args['message'],
                "type" => $args['type'],
                "senderId" => $args['senderId'],
                "sendTime" => $date,
                "url" => $args['url']
            );
            $notification->createNotification($notificationArgs, array("projectId", "message", "type", "senderId", "sendTime", "url"));
            
            $notifyConditions = array(
                "projectId" => $projectId,
                "senderId" => $args['senderId'],
                "sendTime" => $date
            );
            $newNotification = $notification->getNotification($notifyConditions, array("projectId", "senderId", "sendTime"));

            if($args['reciveId']){
                $notifyMemberArgs = array(
                    "notificationId" => $newNotification->id,
                    "memberId" => $args['reciveId']
                );
                $notification->setNotifiers($notifyMemberArgs, array("notificationId", "memberId"));
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
                $notification->setNotifiers(array("notificationId" => $notificationId, "memberId" => $member->member_id), array("notificationId", "memberId"));
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}