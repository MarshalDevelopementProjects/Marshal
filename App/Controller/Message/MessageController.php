<?php

namespace App\Controller\Message;

use App\Model\Message;

require __DIR__ . '/../../../vendor/autoload.php';

class MessageController
{
    // public $key = 'mysecretkey12345';

/*    public function encryptMessage($message) {
        $key = 'mysecretkey12345';
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
      
        $encrypted = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
      
        $encrypted_base64 = base64_encode($encrypted);
        $iv_base64 = base64_encode($iv);
      
        return $encrypted_base64 . ':' . $iv_base64;
    }
      
    public function decryptMessage($encrypted_message) {
        $key = 'mysecretkey12345';
        list($encrypted_base64, $iv_base64) = explode(':', $encrypted_message);
      
        $encrypted = base64_decode($encrypted_base64);
        $iv = base64_decode($iv_base64);
      
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
      
        return $decrypted;
    }*/

    public function send(array $args, array $keys):bool{
        
        $message = new Message();
        // $args->msg = encryptMessage($args->msg);

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
        $message = new Message();
        try {
            $messages = $message->getMessages($condition);
            // foreach($messages as $message) {
            //     $message->msg = decryptMessage($message->msg);
            // }
            // var_dump($messages);
            return $messages;
        } catch (\Throwable $th) {
            // return array();
            throw $th;
        }
    }
}
