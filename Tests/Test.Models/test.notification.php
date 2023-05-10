<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Notification;

function test_create_notification() {
    $notification = new Notification();

    $args = array(
        "project_id" => 4,
        "message" => "New notification",
        "type" => "notification",
        "sender_id" => 1,
        "send_time" => "2023-05-09 12:34:56",
        "url" => "http://localhost/public/user/project?id=4"
    );
    $keys = array("project_id", "message", "type", "sender_id", "send_time", "url");
    $table = "notifications";

    // Test case 1: successful insert
    echo('<br>');
    echo('<br>');
    echo("Test case 1.1: successful insert");
    echo('<br>================================================');
    echo('<br>');
    $result = $notification->create($args, $keys, $table);
    if($result){
        echo('Test case 1.1 passed');
        echo('<br>');
    }else{
        echo('Test case 1.1 failed');
        echo('<br>');
    }

    // Test case 2: insert with invalid table
    echo('<br>');
    echo('<br>');
    echo("Test case 1.2: insert with invalid table");
    echo('<br>================================================');
    echo('<br>');
    $table = "invalid_table";
 
    if($notification->create($args, $keys, $table)){
        echo('Test case 1.2 failed');
        echo('<br>');
    }else{
        echo('Test case 1.2 passed');
        echo('<br>');
    }
   

    // Test case 3: insert with missing keys
    echo('<br>');
    echo('<br>');
    echo("Test case 1.3: insert with missing keys");
    echo('<br>================================================');
    echo('<br>');
    $keys = array("project_id", "message", "type", "sender_id", "send_time");
    
    if($notification->create($args, $keys, $table)){
        echo('Test case 1.3 failed');
        echo('<br>');
    }else{
        echo('Test case 1.3 passed');
        echo('<br>');
    }

    // Test case 4: insert with extra keys
    echo('<br>');
    echo('<br>');
    echo("Test case 1.4: insert with extra keys");
    echo('<br>================================================');
    echo('<br>');
    $keys = array("project_id", "message", "type", "sender_id", "send_time", "url", "extra_key");
    if($notification->create($args, $keys, $table)){
        echo('Test case 1.4 failed');
        echo('<br>');
    }else{
        echo('Test case 1.4 passed');
        echo('<br>');
    }
}

test_create_notification();

function test_get_notification_with_valid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.1: test get notification with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "id" => 17
    );
    $keys = array("id");

    $notification = new Notification();
    $result = $notification->getNotification($args, $keys);
 
    var_dump($result);
    
    if(assert(is_object($result) || is_array($result))){
        echo('Tese case 2.1 passed');
        echo('<br>');
    }else{
        echo('tese case 2.1 failed');
        echo('<br>');
    }
}
function test_get_task_with_missing_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.2: test get notification with missing args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "id" => 4
    );
    $keys = array("id", "message");

    $notification = new Notification();
    $result = $notification->getNotification($args, $keys);

    if(assert(empty($result), "Returned value should be an empty array")){
        echo('Tese case 2.2 passed');
        echo('<br>');
    }else{
        echo('tese case 2.2 failed');
        echo('<br>');
    }
}
function test_get_task_with_invalid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.3: test get notification with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "id" => 5600
    );
    $keys = array("id");

    $notification = new Notification();
    $result = $notification->getNotification($args, $keys);

    if(assert(empty($result), "Returned value should be an empty array")){
        echo('Tese case 2.3 passed');
        echo('<br>');
    }else{
        echo('tese case 2.3 failed');
        echo('<br>');
    }
}
test_get_notification_with_valid_arguments();
test_get_task_with_missing_arguments();
test_get_task_with_invalid_arguments();


function test_get_notifications(){
    echo('<br>');
    echo('<br>');
    echo("Test case 3.1: test get notifications with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = " WHERE id IN (SELECT notification_id FROM notification_recievers WHERE member_id = 1 AND isRead = 0)";

    $results = $notification->getNotifications($condition);
    if(assert(is_array($results))){
        echo('Tese case 3.1 passed');
        echo('<br>');
    }else{
        echo('tese case 3.1 failed');
        echo('<br>');
    }

    // get notifications with empty condition
    $condition = "";

    echo('<br>');
    echo('<br>');
    echo("Test case 3.2: test get notifications with empty condition");
    echo('<br>======================================================');
    echo('<br>');

    $results = $notification->getNotifications($condition);
    if(assert(is_array($results), "Returned value should be an empty array")){
        echo('Tese case 3.2 passed');
        echo('<br>');
    }else{
        echo('tese case 3.2 failed');
        echo('<br>');
    }

    // get notifications with null condition
    $condition = null;

    echo('<br>');
    echo('<br>');
    echo("Test case 3.3: test get notifications with null condition");
    echo('<br>======================================================');
    echo('<br>');

    $results = $notification->getNotifications($condition);
    if(assert(is_array($results), "Returned value should be an empty array")){
        echo('Tese case 3.3 passed');
        echo('<br>');
    }else{
        echo('tese case 3.3 failed');
        echo('<br>');
    }
}
test_get_notifications();


function test_delete_notification(){
    echo('<br>');
    echo('<br>');
    echo("Test case 4.1: test delete notification with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = "WHERE id IN (SELECT notification_id FROM task_notification WHERE task_id = 17)";
    $result = $notification->deleteNotification($condition, "notifications");

    if(assert($result, "Returned value should be true")){
        echo('Tese case 4.1 passed');
        echo('<br>');
    }else{
        echo('tese case 4.1 failed');
        echo('<br>');
    }

    // with invalid table name 
    echo('<br>');
    echo('<br>');
    echo("Test case 4.2: test delete notification with invalid table name");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = "WHERE id IN (SELECT notification_id FROM task_notification WHERE task_id = 17)";
    $result = $notification->deleteNotification($condition, "invalid_notifications");

    if(assert(!$result, "Returned value should be false")){
        echo('Tese case 4.2 passed');
        echo('<br>');
    }else{
        echo('tese case 4.2 failed');
        echo('<br>');
    }

    // with empty condition 
    echo('<br>');
    echo('<br>');
    echo("Test case 4.3: test delete notification with empty condition");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = "";
    $result = $notification->deleteNotification($condition, "notifications");

    if(assert(!$result, "Returned value should be false")){
        echo('Tese case 4.3 passed');
        echo('<br>');
    }else{
        echo('tese case 4.3 failed');
        echo('<br>');
    }

    // with null condition 
    echo('<br>');
    echo('<br>');
    echo("Test case 4.4: test delete notification with null condition");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = null;
    $result = $notification->deleteNotification($condition, "notifications");

    if(assert(!$result, "Returned value should be false")){
        echo('Tese case 4.4 passed');
        echo('<br>');
    }else{
        echo('tese case 4.4 failed');
        echo('<br>');
    }

    // with invalid condition
    echo('<br>');
    echo('<br>');
    echo("Test case 4.5: test delete notification with invalid table name");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $condition = "WHERE id IN (SELECT notification_ FROM task_notification WHERE task_id = 17)";
    $result = $notification->deleteNotification($condition, "notifications");

    if(assert(!$result, "Returned value should be false")){
        echo('Tese case 4.5 passed');
        echo('<br>');
    }else{
        echo('tese case 4.5 failed');
        echo('<br>');
    }

}

// test_delete_notification();

function test_ReadNotification_Success() {
    echo('<br>');
    echo('<br>');
    echo("Test case 5.1: test read notification with valid argumanets");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $args = array(
        "notification_id" => 131,
        "member_id" => 4
    );
    $result = $notification->readNotification($args);

    if ($result === true) {
        echo "Test case test_ReadNotification_Success passed.";
        echo('<br>');
    } else {
        echo "Test case test_ReadNotification_Success failed.";
        echo('<br>');
    }
}

function test_ReadNotification_InvalidNotificationId() {
    echo('<br>');
    echo('<br>');
    echo("Test case 5.2: test read notification with invalid argumanets");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $args = array(
        "notification_id" => "invalid",
        "member_id" => 4
    );
    $result = $notification->readNotification($args);

    if ($result === false) {
        echo "Test case test_ReadNotification_InvalidNotificationId passed.";
        echo('<br>');
    } else {
        echo "Test case test_ReadNotification_InvalidNotificationId failed.";
        echo('<br>');
    }
}

function test_ReadNotification_MissingNotificationId() {
    echo('<br>');
    echo('<br>');
    echo("Test case 5.3: test read notification with missing argumanets");
    echo('<br>======================================================');
    echo('<br>');

    $notification = new Notification();
    $args = array(
        "member_id" => 4
    );
    $result = $notification->readNotification($args);

    if ($result === false) {
        echo "Test case test_ReadNotification_MissingNotificationId passed.";
        echo('<br>');
    } else {
        echo "Test case test_ReadNotification_MissingNotificationId failed.";
        echo('<br>');
    }
}


test_ReadNotification_Success();
test_ReadNotification_InvalidNotificationId();
test_ReadNotification_MissingNotificationId();