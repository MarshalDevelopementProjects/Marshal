<?php

namespace App\Controller\Message;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\Controller;
use App\Model\Message;
use Core\Validator\Validator;

require __DIR__ . '/../../../vendor/autoload.php';

class MessageController
{
    public function defaultAction(Object|array|string|int $optional = null){
    }

    public function send(array $args, array $keys):bool{
        
        // from this we have to do encrypt message

        $message = new Message();
        try {
            $message->sendMessage($args, $keys);
            return true;
        } catch (\Throwable $th) {
            // return false;
            throw $th;
        }
    }

    public function recieve(string $condition):object|array|bool
    {
        // from this we have to do decrypt message

        $message = new Message();
        try {
            return $message->getMessages($condition);
        } catch (\Throwable $th) {
            // return array();
            throw $th;
        }
    }
}
