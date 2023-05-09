<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Task;

session_start();
$_SESSION['project_id'] = 4;
$_SESSION['group_id'] = 1;

function test_create_task_with_valid_data(){
    echo('<br>');
    echo('<br>');
    echo("Test case 1.1: create task with Valid data");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();

    $data = array(
        "project_id" => 4,
        "description" => "Task description",
        "deadline" => "2023-05-31 23:59:59",
        "task_name" => "Task name",
        "priority" => "high",
        "status" => "TO-DO"
    );
    $result = $task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status"));
    if(assert($result === true, "Test case 1: Expected true, but got " . var_export($result, true))){
        echo('Tese case 1.1 passed');
        echo('<br>');
    }else{
        echo('tese case 1.1 failed');
        echo('<br>');
    }
}
function test_create_task_with_invalid_project_id(){
    echo('<br>');
    echo('<br>');
    echo("Test case 1.2: create task with invalid project id");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();

    $data = array(
        "project_id" => "invalid",
        "description" => "Task description",
        "deadline" => "2023-05-31 23:59:59",
        "task_name" => "Task name",
        "priority" => "high",
        "status" => "TO-DO"
    );
    $result = $task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status"));
    if(assert($result === false, "Test case 1.2: Expected false, but got " . var_export($result, true))){
        echo('Tese case 1.2 passed');
        echo('<br>');
    }else{
        echo('tese case 1.2 failed');
        echo('<br>');
    }
}
function test_create_task_with_missing_arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 1.3: create task with missing arguments");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();

    $data = array(
        "project_id" => 4,
        "description" => "Task description",
        "deadline" => "2023-05-31 23:59:59",
        "priority" => "high",
        "status" => "TO-DO"
    );
    $result = $task->createTask($data, array("project_id", "description", "deadline", "task_name", "priority", "status"));
    if(assert($result === false, "Test case 1,3: Expected false, but got " . var_export($result, true))){
        echo('Tese case 1.3 passed');
        echo('<br>');
    }else{
        echo('tese case 1.3 failed');
        echo('<br>');
    }
}

test_create_task_with_valid_data();
test_create_task_with_invalid_project_id();
test_create_task_with_missing_arguments();


function test_Get_All_Tasks_For_Project() {

    echo('<br>');
    echo('<br>');
    echo("Test case 2.1: test get all tasks for project");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4,
        "task_type" => "project"
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(is_array($result))){
        echo('Tese case 2.1 passed');
        echo('<br>');
    }else{
        echo('tese case 2.1 failed');
        echo('<br>');
    }
}

function test_Get_All_Tasks_For_Group() {

    echo('<br>');
    echo('<br>');
    echo("Test case 2.2: test get all tasks for group");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4,
        "task_type" => "group",
        "group_id" => 1
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(is_array($result))){
        echo('Tese case 2.2 passed');
        echo('<br>');
    }else{
        echo('tese case 2.2 failed');
        echo('<br>');
    }
}
function test_Get_All_Tasks_with_missing_args_for_project_tasks(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.3: test get all tasks with missing args for project tasks");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "task_type" => "project"
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(!$result)){
        echo('Tese case 2.3 passed');
        echo('<br>');
    }else{
        echo('tese case 2.3 failed');
        echo('<br>');
    }
}
function test_Get_All_Tasks_with_missing_args_for_group_tasks(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.4: test get all tasks with missing args for group tasks");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4,
        "task_type" => "group"
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(!$result)){
        echo('Tese case 2.4 passed');
        echo('<br>');
    }else{
        echo('tese case 2.4 failed');
        echo('<br>');
    }
}

function test_Get_All_Tasks_with_invalid_args_for_project_tasks(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.5: test get all tasks with invalid args for project tasks");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 1,
        "task_type" => "project"
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(!$result)){
        echo('Tese case 2.5 passed');
        echo('<br>');
    }else{
        echo('tese case 2.5 failed');
        echo('<br>');
    }
}
function test_Get_All_Tasks_with_invalid_args_for_group_tasks(){
    echo('<br>');
    echo('<br>');
    echo("Test case 2.6: test get all tasks with invalid args for group tasks");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 1,
        "task_type" => "group",
        "group_id" => 5
    );
    $task = new Task();
    $result = $task->getAllTasks($args);
    if(assert(!$result)){
        echo('Tese case 2.6 passed');
        echo('<br>');
    }else{
        echo('tese case 2.6 failed');
        echo('<br>');
    }
}


test_Get_All_Tasks_For_Project();
test_Get_All_Tasks_For_Group();
test_Get_All_Tasks_with_missing_args_for_project_tasks();
test_Get_All_Tasks_with_missing_args_for_group_tasks();
test_Get_All_Tasks_with_invalid_args_for_project_tasks();
test_Get_All_Tasks_with_invalid_args_for_group_tasks();

function test_get_tasks_with_valid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 3.1: test get tasks with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4,
        "status" => "ONGOING"
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTasks($args, $keys);
    if(assert(is_array($result))){
        echo('Tese case 3.1 passed');
        echo('<br>');
    }else{
        echo('tese case 3.1 failed');
        echo('<br>');
    }
}
function test_get_tasks_with_missing_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 3.2: test get tasks with missing args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTasks($args, $keys);
    if(assert(!$result)){
        echo('Tese case 3.2 passed');
        echo('<br>');
    }else{
        echo('tese case 3.2 failed');
        echo('<br>');
    }
}
function test_get_tasks_with_invalid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 3.3: test get tasks with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 56,
        "status" => "ONGOING"
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTasks($args, $keys);
    if(assert(!$result)){
        echo('Tese case 3.3 passed');
        echo('<br>');
    }else{
        echo('tese case 3.3 failed');
        echo('<br>');
    }
}
test_get_tasks_with_valid_arguments();
test_get_tasks_with_missing_arguments();
test_get_tasks_with_invalid_arguments();

function test_get_task_with_valid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 4.1: test get task with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4,
        "task_name" => "Sketch the circuit design"
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTask($args, $keys);
    if(assert(is_array($result))){
        echo('Tese case 4.1 passed');
        echo('<br>');
    }else{
        echo('tese case 4.1 failed');
        echo('<br>');
    }
}
function test_get_task_with_missing_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 4.2: test get task with missing args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 4
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTask($args, $keys);
    if(assert(empty($result), "Returned value should be an empty array")){
        echo('Tese case 4.2 passed');
        echo('<br>');
    }else{
        echo('tese case 4.2 failed');
        echo('<br>');
    }
}
function test_get_task_with_invalid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 4.3: test get task with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    $args = array(
        "project_id" => 56,
        "status" => "ONGOING"
    );
    $keys = array("project_id", "status");

    $task = new Task();
    $result = $task->getTask($args, $keys);
    if(assert(empty($result), "Returned value should be an empty array")){
        echo('Tese case 4.3 passed');
        echo('<br>');
    }else{
        echo('tese case 4.3 failed');
        echo('<br>');
    }
}
test_get_task_with_valid_arguments();
test_get_task_with_missing_arguments();
test_get_task_with_invalid_arguments();

function test_Update_Task_with_valid_arguments() {

    echo('<br>');
    echo('<br>');
    echo("Test case 5.1: test update task with valid args");
    echo('<br>======================================================');
    echo('<br>');

    // create a new task
    $task = new Task();
    $args = array(
        "project_id" => 4,
        "description" => "test update task functionality",
        "deadline" => "2023-06-30",
        "task_name" => "Test update task2",
        "priority" => "high",
        "status" => "TO-DO"
    );
    $keys = array("project_id", "description", "deadline", "task_name", "priority", "status");
    // $task->createTask($args, $keys);

    // update the task status
    $args = array(
        "priority" => "medium",
        "project_id" => 4,
        "task_name" => "Test update task2"
    );
    $conditions = array("project_id", "task_name");
    $updates = array("priority");
    $result = $task->updateTask($args, $updates, $conditions);

    // check if the task was updated successfully
    if(assert($result)){
        echo('Tese case 5.1 passed');
        echo('<br>');
    }else{
        echo('tese case 5.1 failed');
        echo('<br>');
    }
    // retrieve the updated task and check if the status was changed
    $args = array(
        "project_id" => 4,
        "task_name" => "Test update task2"
    );
    $keys = array("project_id", "task_name");
    $updatedTask = $task->getTask($args, $keys);

    if(!empty($updatedTask) && assert("medium" == $updatedTask->priority)){
        echo('Tese case 5.1 values are -> '. $updatedTask->priority);
        echo('<br>');
    }else{
        echo('tese case 5.1 failed');
        echo('<br>');
    }
}
function test_Update_Task_with_missing_arguments(){
    
    echo('<br>');
    echo('<br>');
    echo("Test case 5.2: test update task with missing args");
    echo('<br>======================================================');
    echo('<br>');

    // create a new task
    $task = new Task();

    // update the task status
    $args = array(
        "status" => "DONE",
        "project_id" => 4
    );
    $conditions = array("project_id", "task_name");
    $updates = array("status");
    $result = $task->updateTask($args, $updates, $conditions);

    // check if the task was updated successfully
    if(assert(!$result)){
        echo('Tese case 5.2 passed');
        echo('<br>');
    }else{
        echo('tese case 5.2 failed');
        echo('<br>');
    }
}

test_Update_Task_with_valid_arguments();
test_Update_Task_with_missing_arguments();

function test_complete_task_with_valid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 6.1: test complete task with valid args");
    echo('<br>======================================================');
    echo('<br>');

    // create a new task
    $task = new Task();
    $args = array(
        "task_id" => 18,
        "confirmation_type" => "message",
        "confirmation_message" => "Task completed successfully.",
        "date" => "2023-05-09",
        "time" => "14:30:00"
    );
    
    // Call the function and check the result
    $result = $task->completeTask($args);
    if(assert($result)){
        echo('Tese case 6.1 passed');
        echo('<br>');
    }else{
        echo('tese case 6.1 failed');
        echo('<br>');
    }
}

function test_complete_task_with_missing_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 6.2: test complete task with missing args");
    echo('<br>======================================================');
    echo('<br>');

    // create a new task
    $task = new Task();
    $args = array(
        "task_id" => 12,
        "confirmation_type" => "message",
        "date" => "2023-05-09",
        "time" => "14:30:00"
    );
    
    // Call the function and check the result
    $result = $task->completeTask($args);
    if(assert(!$result)){
        echo('Tese case 6.2 passed');
        echo('<br>');
    }else{
        echo('tese case 6.2 failed');
        echo('<br>');
    }
}

function test_complete_task_with_invalid_arguments(){
    echo('<br>');
    echo('<br>');
    echo("Test case 6.3: test complete task with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    // create a new task
    $task = new Task();
    $args = array(
        "task_id" => 18000,
        "confirmation_type" => "message",
        "confirmation_message" => "Task completed successfully.",
        "date" => "2023-05-09",
        "time" => "14:30:00"
    );
    
    // Call the function and check the result
    $result = $task->completeTask($args);
    if(assert(!$result)){
        echo('Tese case 6.3 passed');
        echo('<br>');
    }else{
        echo('tese case 6.3 failed');
        echo('<br>');
    }
}

// test_complete_task_with_valid_arguments();
test_complete_task_with_missing_arguments();
test_complete_task_with_invalid_arguments();

function test_Get_Task_Completed_Details_With_Valid_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 7.1: test get completed task details with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();
    
    // Define valid arguments
    $args = array(
        "task_id" => 17
    );
    
    $result = $task->getTaskCompletedDetails($args);

    if(assert(is_object($result))){
        echo('Tese case 7.1 passed');
        echo('<br>');
    }else{
        echo('tese case 7.1 failed');
        echo('<br>');
    }
}

function test_Get_Task_Completed_Details_With_Invalid_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 7.2: test get completed task details with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();
    
    // Define invalid arguments
    $args = array(
        "task_id" => -1
    );
    
    $result = $task->getTaskCompletedDetails($args);
    
    if(assert(!$result)){
        echo('Tese case 7.2 passed');
        echo('<br>');
    }else{
        echo('tese case 7.2 failed');
        echo('<br>');
    }
}

function test_Get_Task_Completed_Details_With_Missing_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 7.3: test get completed task details missing args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();    
    $args = array();
    
    $result = $task->getTaskCompletedDetails($args);
    
    if(assert(!$result)){
        echo('Tese case 7.3 passed');
        echo('<br>');
    }else{
        echo('tese case 7.3 failed');
        echo('<br>');
    }
}

test_Get_Task_Completed_Details_With_Valid_Arguments();
test_Get_Task_Completed_Details_With_Invalid_Arguments();
test_Get_Task_Completed_Details_With_Missing_Arguments();

function test_Add_Group_To_Task_With_Valid_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 8.1: test add group to task with valid args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();
    
    // Define valid arguments
    $args = array(
        "task_id" => 25,
        "group_id" => 1
    );
    
    $result = $task->addGroupToTask($args);
    
    if($result === true){
        echo('Tese case 8.1 passed');
        echo('<br>');
    }else{
        echo('tese case 8.1 failed');
        echo('<br>');
    }
}

function test_Add_Group_To_Task_With_Missing_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 8.2: test add group to task with missing args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();
    
    // Define invalid arguments
    $args = array(
        "group_id" => 1
    );
    
    $result = $task->addGroupToTask($args);
    
    if($result === false){
        echo('Tese case 8.2 passed');
        echo('<br>');
    }else{
        echo('tese case 8.2 failed');
        echo('<br>');
    }
}

function test_Add_Group_To_Task_With_Invalid_Arguments() {
    echo('<br>');
    echo('<br>');
    echo("Test case 8.3: test add group to task with invalid args");
    echo('<br>======================================================');
    echo('<br>');

    $task = new Task();
    
    // Define invalid arguments
    $args = array(
        "task_id" => "abc",
        "group_id" => 1
    );
    
    $result = $task->addGroupToTask($args);
    
    if($result === false){
        echo('Tese case 8.3 passed');
        echo('<br>');
    }else{
        echo('tese case 8.3 failed');
        echo('<br>');
    }
}

test_Add_Group_To_Task_With_Valid_Arguments();
test_Add_Group_To_Task_With_Missing_Arguments();
test_Add_Group_To_Task_With_Invalid_Arguments();