<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\Notification\NotificationController;

// Test function for the setNotification() function
function test_Set_Notification_With_Valid_Arguments(){
    // Test Case 1: Valid arguments with a recipient

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

    $args1 = array(
        "message" => "Test message",
        "type" => "notification",
        "sender_id" => 1,
        "url" => "http://localhost/public/user/project",
        "recive_id" => 2
      );
      $result1 = $notification->setNotification($args1);
      var_dump( assert(is_int($result1), "Test Case 1: Returns an integer value"),"Test Case 1: Returns an integer value");
}
function test_Set_Notification_Without_ReciveId() {

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

    // Test Case 2: Valid arguments without a recipient
    $args2 = array(
        "message" => "Test message",
        "type" => "notification",
        "sender_id" => 1,
        "url" => "http://localhost/public/user/project",
        "recive_id" => null
      );
      $result2 = $notification->setNotification($args2);
      var_dump(assert(is_int($result2), "Test Case 2: Returns an integer value"), "Test Case 2: Returns an integer value");
}

function test_Set_Notification_With_Missing_Arguments() {

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

    // Test Case 3: Missing required arguments
    $args3 = array(
        "message" => "Test message",
        "type" => "notification",
        "url" => "http://localhost/public/user/project"
      );
      try {
        $notification->setNotification($args3);
      } catch (Throwable $e) {
        $expectedMessage = "Missing argument: sender_id";
        var_dump($e->getMessage());
        var_dump(assert($e->getMessage() === $expectedMessage, "Test Case 3: Throws an exception with the expected message"), "Test Case 3: Throws an exception with the expected message");
      }
}

function test_Set_Notification_With_Invalid_reciveId(){

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

    // Test Case 4: Invalid recipient ID
    $args4 = array(
        "message" => "Test message",
        "type" => "notification",
        "sender_id" => 1,
        "url" => "http://localhost/public/user/project",
        "recive_id" => "invalid"
      );
      try {
        $notification->setNotification($args4);
      } catch (Throwable $e) {
        $expectedMessage = "Invalid argument type for recive_id";
        var_dump($e->getMessage());
        var_dump(assert($e->getMessage() === $expectedMessage, "Test Case 4: Throws an exception with the expected message"),"Test Case 4: Throws an exception with the expected message");
      }
}
function test_Set_Notification_With_Non_Existent_sender(){

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

    // Test Case 5: Non-existent sender ID
    $args5 = array(
        "message" => "Test message",
        "type" => "Test type",
        "sender_id" => 9999,
        "url" => "http://localhost/public/user/project",
        "recive_id" => null
      );
      try {
        $notification->setNotification($args5);
      } catch (Throwable $e) {
        $expectedMessage = "Sender with ID 9999 does not exist";
        var_dump($e->getMessage());
        var_dump(assert($e->getMessage() === $expectedMessage, "Test Case 5: Throws an exception with the expected message"), "Test Case 5: Throws an exception with the expected message");
      }
}

function test_Set_Notification_With_Invalid_URL_Format(){

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    // Initialize the Notification object
    $notification = new NotificationController();

     // Test Case 6: Invalid URL format
     $args6 = array(
        "message" => "Test message",
        "type" => "Test type",
        "sender_id" => 1,
        "url" => "invalid-url",
        "recive_id" => null
      );
      try {
        $notification->setNotification($args6);
      } catch (Throwable $e) {
        $expectedMessage = "Invalid URL format";
        var_dump($e->getMessage());
        var_dump(assert($e->getMessage() === $expectedMessage, "Test Case 6: Throws an exception with the expected message"),"Test Case 6: Throws an exception with the expected message");
      }
    
}

//test the boardcast notification method

function test_Boardcast_Notification_With_Empty_Members(){

    $notification = new NotificationController();

    // Test case 1: Test with empty members array
    $notificationId = 1;
    $members = array();
    $result = $notification->boardcastNotification($notificationId, $members);
    var_dump(assert($result === false, "Test case 1 failed: expected false"),"Test case 1");
}

function test_Boardcast_Notification_With_One_Member(){

    $notification = new NotificationController();
    
    // Test case 2: Test with one member
    $notificationId = 1;
    $members = array(
        array("member_id" => 1)
    );
    // convert it to object array
    $members = array_map(function($item) {
        return (object)$item;
    }, $members);

    $result = $notification->boardcastNotification($notificationId, $members);
    var_dump(assert($result === true, "Test case 2 failed: expected true"),"Test case 2");
}

function test_Boardcast_Notification_With_More_Members(){

    $notification = new NotificationController();

    // Test case 3: Test with multiple members
    $notificationId = 1;
    $members = array(
        array("member_id" => 1),
        array("member_id" => 2),
        array("member_id" => 3),
    );
    // convert it to object array
    $members = array_map(function($item) {
        return (object)$item;
    }, $members);

    $result = $notification->boardcastNotification($notificationId, $members);
    var_dump(assert($result === true, "Test case 3 failed: expected true"),"Test case 3");
}

function test_Boardcast_Notification_With_Invalid_NotificationId(){
    $notification = new NotificationController();

    // Test case 4: Test with invalid notification ID
    $notificationId = -1;
    $members = array(
        array("member_id" => 1),
    );
    // convert it to object array
    $members = array_map(function($item) {
        return (object)$item;
    }, $members);

    $result = $notification->boardcastNotification($notificationId, $members);
    var_dump(assert($result === false, "Test case 4 failed: expected false"),"Test case 4");
}


function test_Boardcast_Notification_With_Invalid_member_Id(){

    // Start the session (if it hasn't been started already)
    session_start();

    $_SESSION['project_id'] = 4;
    $notification = new NotificationController();

    // Test case 5: Test with invalid member IDs
    $notificationId = 4;
    $members = array(
        array("member_id" => -1),
        array("member_id" => 0),
        array("member_id" => "invalid"),
    );
    // convert it to object array
    $members = array_map(function($item) {
        return (object)$item;
    }, $members);

    $result = $notification->boardcastNotification($notificationId, $members);
    var_dump(assert($result === false, "Test case 5 failed: expected false"), "Test case 5 ");
}


test_Boardcast_Notification_With_Invalid_member_Id();