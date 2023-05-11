<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Group;

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
        case 'is float':
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

function test_create_group(){
    $test_case = '1.1 create group with valid arguments';

    get_Unit_Test_Format_Heading($test_case);

    $group = new Group();

    $args = array(
        "group_name" => 'test group',
        "task_name" => 'test group task',
        "description" => 'test group description',
        "project_id" => 4,
        "leader_id" => 1
    );
    $keys = array("group_name", "task_name", "description", "project_id", "leader_id");

    $result = $group->createGroup($args, $keys);
    get_test_result($test_case, $result, 'is true');




    $test_case = "1.2 create group with invalid arguments";
    get_Unit_Test_Format_Heading($test_case);

    $keys = array("group_name", "task_name", "description", "pject_id", "leader_id");
    $result = $group->createGroup($args, $keys);
    get_test_result($test_case, $result, 'is false');




    $test_case = "1.3 create group with missing arguments";
    get_Unit_Test_Format_Heading($test_case);

    $args = array(
        "group_name" => 'test group',
        "task_name" => 'test group task',
        "description" => 'test group description',
        "leader_id" => 1
    );
    $result = $group->createGroup($args, $keys);
    get_test_result($test_case, $result, 'is false');


    
    $test_case = "1.4 Send message with null arguments";
    get_Unit_Test_Format_Heading($test_case);

    $args = array(
        "group_name" => 'test group',
        "task_name" => 'test group task',
        "description" => 'test group description',
        "project_id" => 4,
        "leader_id" => null
    );
    $result = $group->createGroup($args, $keys);
    get_test_result($test_case, $result, 'is false');
}

test_create_group();

function test_get_all_groups(){
    $group = new Group();

    $test_case = '2.1 get all groups with valid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getAllGroups(array("project_id" => 4, "finished" => 0), array("project_id", "finished"));
    get_test_result($test_case, $result, 'is array');



    $test_case = '2.2 get all groups with invalid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getAllGroups(array("project_id" => 4, "finished" => 0), array("prect_id", "finished"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '2.3 get all groups with missing arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getAllGroups(array("project_id" => 4), array("project_id", "finished"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '2.4 get all groups with null arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getAllGroups(array("project_id" => null, "finished" => 0), array("project_id", "finished"));
    get_test_result($test_case, $result, 'is false');

}

test_get_all_groups();

function test_get_group(){
    $group = new Group();

    $test_case = '3.1 get group with valid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroup(array("group_name" => 'Make Responsive', "project_id" => 4), array("group_name", "project_id"));
    get_test_result($test_case, $result, 'is object');



    
    $test_case = '3.2 get group with invalid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroup(array("group_name" => 'Make Responsive', "project_id" => 4), array("group_mmname", "project_id"));
    get_test_result($test_case, $result, 'is false');




    $test_case = '3.3 get group with missing arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroup(array("group_name" => 'Make Responsive',), array("group_name", "project_id"));
    get_test_result($test_case, $result, 'is false');




    $test_case = '3.4 get group with null arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroup(array("group_name" => 'Make Responsive', "project_id" => null), array("group_name", "project_id"));
    get_test_result($test_case, $result, 'is false');

}
test_get_group();

function test_update_group(){

    $group = new Group();

    $test_case = '3.1 update group with valid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->updateGroup(array("id" => 2, "finished" => 1), array("finished"), array("id"));
    get_test_result($test_case, $result, 'is true');


    $test_case = '3.1 update group with invalid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->updateGroup(array("id" => 2, "finished" => 1), array("fininshed"), array("id"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '3.1 update group with missing arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->updateGroup(array("id" => 2), array("finished"), array("id"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '3.1 update group with null arguments';
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->updateGroup(array("id" => null, "finished" => 1), array("finished"), array("id"));
    get_test_result($test_case, $result, 'is false');

}

test_update_group();

function test_add_group_member(){
    $group = new Group();

    $test_case = '4.1 add group member with valid arguments';   
    get_Unit_Test_Format_Heading($test_case); 

    $memberArgs = array(
        "group_id" => 1,
        "member_id" => 3,
        "role" => "MEMBER",
        "joined" => date("Y-m-d H:i:s")
    );
    $result = $group->addGroupMember($memberArgs, array("group_id", "member_id", "role", "joined"));
    get_test_result($test_case, $result, 'is true');



    $test_case = '4.2 add group member with invalid arguments';   
    get_Unit_Test_Format_Heading($test_case); 

    $result = $group->addGroupMember($memberArgs, array("groupww_id", "member_id", "role", "joined"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '4.3 add group member with missing arguments';   
    get_Unit_Test_Format_Heading($test_case); 

    $memberArgs = array(
        "group_id" => 1,
        "member_id" => 3,
        "joined" => date("Y-m-d H:i:s")
    );
    $result = $group->addGroupMember($memberArgs, array("group_id", "member_id", "role", "joined"));
    get_test_result($test_case, $result, 'is false');



    $test_case = '4.4 add group member with null arguments';   
    get_Unit_Test_Format_Heading($test_case); 

    $memberArgs = array(
        "group_id" => null,
        "member_id" => 3,
        "role" => "MEMBER",
        "joined" => date("Y-m-d H:i:s")
    );
    $result = $group->addGroupMember($memberArgs, array("group_id", "member_id", "role", "joined"));
    get_test_result($test_case, $result, 'is false');

}

test_add_group_member();

function test_get_group_progress(){
    $group = new Group();

    $test_case = '5.1 get group progress with valid arguments';   
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroupProgress(1);
    get_test_result($test_case, $result, 'is float');



    $test_case = '5.2 get group progress with invalid arguments';   
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroupProgress(1000);
    get_test_result($test_case, $result, 'is false');
    
    

    $test_case = '5.3 get group progress with null arguments';   
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroupProgress(null);
    get_test_result($test_case, $result, 'is false');
}

test_get_group_progress();


function test_get_group_staticstic(){
    $group = new Group();

    $test_case = '6.1 get group staticstic with valid arguments';   
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroupStatistics(1);
    get_test_result($test_case, $result, 'is true');



    $test_case = '6.2 get group staticstic with null arguments';   
    get_Unit_Test_Format_Heading($test_case);

    $result = $group->getGroupStatistics(null);
    get_test_result($test_case, $result, 'is false');
}

test_get_group_staticstic();