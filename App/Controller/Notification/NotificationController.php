<?php

namespace App\Controller\Notification;

use App\Model\Notification;
use App\Model\Project;

class NotificationController{
    
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

            // check if there is any missing arguments
            $requiredKeys = array('message', 'type', 'sender_id', 'url');

            foreach ($requiredKeys as $key) {
                if (!isset($args[$key]) || empty($args[$key])) {
                    throw new \Exception('Missing argument: ' . $key);
                }
            }

            $date = date("Y-m-d H:i:s");
            $projectId = $_SESSION['project_id'];

            // check sender id is valid or not
            $project = new Project();

            if(!$project->readUserRole(member_id: $args['sender_id'], project_id: $_SESSION['project_id'])){
                throw new \Exception("Sender with ID ". $args['sender_id'] ." does not exist");
            }

            // check reciver id is valid or not
            if($args['recive_id'] != null && !$project->readUserRole(member_id: $args['recive_id'], project_id: $_SESSION['project_id'])){    
                throw new \Exception("Invalid argument type for recive id");
            }

            // check URL
            if (strpos($args['url'], "http://localhost/public") !== 0) {
                throw new \Exception("Invalid URL format");
            }

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
        $project = new Project();

        if($notification->getNotification(array("id" => $notificationId), array("id")) != array()){
            if($members){
                try {
                    foreach($members as $member){
                        // var_dump($member->member_id);
                        if(!$project->readUserRole(member_id: $member->member_id, project_id: $_SESSION['project_id'])){
                            return false;
                        }
                        $notification->setNotifiers(array("notification_id" => $notificationId, "member_id" => $member->member_id), array("notification_id", "member_id"));
                    }
                    return true;
                } catch (\Throwable $th) {
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
}