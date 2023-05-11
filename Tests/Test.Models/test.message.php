<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Message;

function get_Unit_Test_Format_Heading(string $test_case):void
{
    echo('<br>');
    echo('<br>');
    echo("Test case : " . $test_case);
    echo('<br>================================================');
    echo('<br>');
}

function get_Unit_Test_Format_Passed_message(string $test_case):void
{
    echo('Test case ' . $test_case . ' passed');
    echo('<br>');
}

function get_Unit_Test_Format_failed_message(string $test_case):void
{
    echo('Test case ' . $test_case . ' failed');
    echo('<br>');
}

function get_test_result(string $test_case, $result, $expected_result_type):void
{
    switch($expected_result_type){
        case 'is empty':
            if(empty($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is object':
            if(is_object($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is array':
            if(is_array($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is false':
            if(!$result){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is true':
            if($result){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        default :
            echo 'test result is failed';
    }
}

function test_send_message(){
    $test_case = "1.1 Send message with valid arguments";
    get_Unit_Test_Format_Heading($test_case);

    $message = new Message();

    $date = date('Y-m-d H:i:s');
    $args = array(
        "sender_id" => 1,
        "stamp" => $date,
        "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE",
        "msg" => "Test message"
    );

    $result = $message->sendMessage($args, array("sender_id", "stamp", "message_type", "msg"));
    get_test_result($test_case, $result, 'is true');


    $test_case = "1.2 Send message with invalid arguments";
    get_Unit_Test_Format_Heading($test_case);

    $args = array(
        "sender_id" => 10000,
        "stamp" => $date,
        "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE",
        "msg" => "Test message"
    );
    $result = $message->sendMessage($args, array("sender_id", "stamp", "message_type", "msg"));
    get_test_result($test_case, $result, 'is false');

    $test_case = "1.3 Send message with missing arguments";
    get_Unit_Test_Format_Heading($test_case);

    $args = array(
        "sender_id" => 10000,
        "stamp" => $date,
        "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE"
    );
    $result = $message->sendMessage($args, array("sender_id", "stamp", "message_type", "msg"));
    get_test_result($test_case, $result, 'is false');


    
    $test_case = "1.4 Send message with null arguments";
    get_Unit_Test_Format_Heading($test_case);

    $args = array(
        "sender_id" => 10000,
        "stamp" => $date,
        "message_type" => "PROJECT_TASK_FEEDBACK_MESSAGE",
        "msg" => null
    );
    $result = $message->sendMessage($args, array("sender_id", "stamp", "message_type", "msg"));
    get_test_result($test_case, $result, 'is false');

}

test_send_message();


function test_get_messages(){
    $message = new Message();

    $condition = "id IN(SELECT message_id FROM `project_announcement` WHERE project_id = 4) ORDER BY `stamp` LIMIT 100";
    $test_case = "2.1 get messages with valid condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getMessages($condition);
    get_test_result($test_case, $result, 'is array');

    $condition = "id IN(SELECT message_id FROM `_announcement` WHERE project_id = 4) ORDER BY `stamp` LIMIT 100";
    $test_case = "2.2 get messages with invalid condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getMessages($condition);
    get_test_result($test_case, $result, 'is empty');

    $condition = "";
    $test_case = "2.3 get messages with empty condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getMessages($condition);
    get_test_result($test_case, $result, 'is empty');

    $condition = null;
    $test_case = "2.4 get messages with null condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getMessages($condition);
    get_test_result($test_case, $result, 'is empty');
}
test_get_messages();

function test_get_announcement_heading(){
    $message = new Message();

    $condition = "project_id = 4 AND message_id = 13";

    $test_case = "3.1 get announcement heading with valid condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getAnnouncementHeading($condition, 'project_announcement');
    get_test_result($test_case, $result, 'is object');

    $condition = "project_id = 4 AD message_id = 13";

    $test_case = "3.2 get announcement heading with invalid condition";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getAnnouncementHeading($condition, 'project_announcement');
    get_test_result($test_case, $result, 'is empty');

    $condition = "project_id = 4 AND message_id = 13";

    $test_case = "3.3 get announcement heading with invalid table name";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getAnnouncementHeading($condition, 'projt_announcement');
    get_test_result($test_case, $result, 'is empty');

    $condition = "";

    $test_case = "3.3 get announcement heading with empty";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getAnnouncementHeading($condition, 'project_announcement');
    get_test_result($test_case, $result, 'is empty');


    $condition = null;

    $test_case = "3.4 get announcement heading with empty";
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->getAnnouncementHeading($condition, 'project_announcement');
    get_test_result($test_case, $result, 'is empty');

}

test_get_announcement_heading();

function test_delete_message(){
    $message = new Message();

    $condition = "WHERE id IN (SELECT message_id FROM project_task_feedback_message WHERE task_id = 19 AND project_id = 4)";
    $test_case = '4.1 delete message with valid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->deleteMessage($condition, 'message');
    get_test_result($test_case, $result, 'is true');

    $condition = "WHERE id IN (SECT message_id FROM project_task_feedback_message WHERE task_id = 19 AND project_id = 4)";
    $test_case = '4.2 delete message with invalid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->deleteMessage($condition, 'message');
    get_test_result($test_case, $result, 'is false');

    $condition = "WHERE id IN (SELECT message_id FROM project_task_feedback_message WHERE task_id = 19 AND project_id = 4)";
    $test_case = '4.3 delete message with invalid table name';
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->deleteMessage($condition, 'melssage');
    get_test_result($test_case, $result, 'is false');

    $condition = "";
    $test_case = '4.4 delete message with empty condition';
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->deleteMessage($condition, 'message');
    get_test_result($test_case, $result, 'is false');

    $condition = null;
    $test_case = '4.5 delete message with null condition';
    get_Unit_Test_Format_Heading($test_case);

    $result = $message->deleteMessage($condition, 'message');
    get_test_result($test_case, $result, 'is false');
}
test_delete_message();