<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\Message\MessageController;

function testSendSuccess() {
    $message = new MessageController;

    $args = array(
        "sender_id" => 1,
        "stamp" => "2022-05-09 10:30:00",
        "message_type" => "PROJECT_TASK_MESSAGE",
        "msg" => "Test task message"
    );
    $keys = array("sender_id", "stamp", "message_type", "msg");

    $result = $message->send($args, $keys);

    if($result == true) {
        echo "Test case testSendSuccess passed.";
    }
    else {
        echo "Test case testSendSuccess failed.";
    }
}

function testSendFailure() {
    $message = new MessageController;

    $args = array(
        "sender_id" => "",
        "stamp" => "2022-05-09 11:30:00",
        "message_type" => "PROJECT_STATUS_UPDATE",
        "msg" => "Test status update message"
    );
    $keys = array("sender_id", "stamp", "message_type", "msg");

    $result = $message->send($args, $keys);

    if($result == false) {
        echo "Test case testSendFailure passed.";
    }
    else {
        echo "Test case testSendFailure failed.";
    }
}

function testSendInvalidMessageType() {
    $message = new MessageController;

    $args = array(
        "sender_id" => 2,
        "stamp" => "2022-05-09 12:30:00",
        "message_type" => "",
        "msg" => "Test message"
    );
    $keys = array("sender_id", "stamp", "message_type", "msg");

    $result = $message->send($args, $keys);

    if($result == false) {
        echo "Test case testSendInvalidMessageType passed.";
    }
    else {
        echo "Test case testSendInvalidMessageType failed.";
    }
}

function testSendInvalidKeys() {
    $message = new MessageController;

    $args = array(
        "sender_id" => 3,
        "stamp" => "2022-05-09 13:30:00",
        "message_type" => "PROJECT_TASK_MESSAGE",
        "msg" => "Test task message"
    );
    $keys = array("sender_id", "stamp", "invalid_key", "msg");

    $result = $message->send($args, $keys);

    if($result == false) {
        echo "Test case testSendInvalidKeys passed.";
    }
    else {
        echo "Test case testSendInvalidKeys failed.";
    }
}

function test_receive_function_with_valid_condition() {
    $message = new MessageController;

    $condition = "id IN(SELECT message_id FROM `project_task_feedback_message` WHERE task_id = 1 AND project_id = 1) ORDER BY `stamp` LIMIT 100";
    $result = $message->recieve($condition);
    
    if(assert(is_array($result) || is_object($result), "Result should be an array or object.")){
        echo "Test case test_receive_function_with_valid_condition passed.";
    }else{
        echo "Test case test_receive_function_with_valid_condition failed";
    }
}

function test_receive_function_with_invalid_condition() {
    $message = new MessageController;

    $condition = "invalid condition";
    try {
        $result = $message->recieve($condition);
    } catch(\Throwable $th) {
        if(assert($th instanceof \Throwable, "Exception should be thrown.")){
            echo "Test case test_receive_function_with_invalid_condition passed.";
        }else{
            echo "Test case test_receive_function_with_invalid_condition failed.";
        }
        return;
    }
}

function test_receive_function_with_empty_condition() {
    $message = new MessageController;

    $condition = "";
    $result = $message->recieve($condition);
    
    if(assert(is_array($result) || is_object($result), "Result should be an array or object.")){
        echo 'Test case test_receive_function_with_empty_condition passed.';
    }else{
        echo 'Test case test_receive_function_with_empty_condition failed.';
    }
}

function test_receive_function_with_null_condition() {
    $message = new MessageController;

    $condition = null;
    $result = $message->recieve($condition);
    if(assert(is_array($result) || is_object($result), "Result should be an array or object.")){
        echo "Test case test_receive_function_with_null_condition passed.";
    }else{
        echo "Test case test_receive_function_with_null_condition failed.";
    }
}


echo('test send function');
echo('<br>');
testSendSuccess();
echo('<br>');
testSendFailure();
echo('<br>');
testSendInvalidMessageType();
echo('<br>');
testSendInvalidKeys();
echo('<br>');
echo('<br>');
echo('test recive function');
echo('<br>');
test_receive_function_with_valid_condition();
echo('<br>');
test_receive_function_with_invalid_condition();
echo('<br>');
test_receive_function_with_empty_condition();
echo('<br>');
test_receive_function_with_null_condition();