<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\Project\ProjectController;
session_start();
$_SESSION['project_id'] = 4;

function test_get_project_tasks_with_valid_arguments(){

    echo("Test Case 1: Test with valid arguments");
    echo('<br>======================================================');
    $project = new ProjectController();
    
    $args = array(
        "project_id" => 4,
        "task_type" => "project"
    );
    $user_id = 1;
    $projectTasks = $project->getProjectTasks($args, $user_id);
    
    // var_dump($projectTasks);
    echo('<br>');
    echo('<br>');

    // Assert that the returned value is an array
    assert(is_array($projectTasks), "Returned value should be an array");
    if (is_array($projectTasks)) {
        echo "Test case 1.1 passed: Returned value is an array.\n";
        echo('<br>');
    } else {
        echo "Test case 1.1 failed: Returned value is not an array.\n";
        echo('<br>');
    }

    // Assert that the returned value has four keys
    assert(count($projectTasks) == 4, "Returned value should have four keys");
    if (count($projectTasks) == 4) {
        echo "Test case 2.2 passed: Returned value has four keys.\n";
        echo('<br>');
    } else {
        echo "Test case 2.2 failed: Returned value does not have four keys.\n";
        echo('<br>');
    }

    // Assert that the value of the 'todoTasks' key is an array
    assert(is_array($projectTasks['todoTasks']), "'todoTasks' key should have an array as its value");
    if (is_array($projectTasks['todoTasks'])) {
        echo "Test case 3.3 passed: 'todoTasks' key has an array as its value.\n";
        echo('<br>');
    } else {
        echo "Test case 3.3 failed: 'todoTasks' key does not have an array as its value.\n";
        echo('<br>');
    }

    // Assert that the value of the 'ongoingTasks' key is an array
    assert(is_array($projectTasks['ongoingTasks']), "'ongoingTasks' key should have an array as its value");
    if (is_array($projectTasks['ongoingTasks'])) {
        echo "Test case 4.4 passed: 'ongoingTasks' key has an array as its value.\n";
        echo('<br>');
    } else {
        echo "Test case 4.4 failed: 'ongoingTasks' key does not have an array as its value.\n";
        echo('<br>');
    }

    // Assert that the value of the 'reviewTasks' key is an array
    assert(is_array($projectTasks['reviewTasks']), "'reviewTasks' key should have an array as its value");
    if (is_array($projectTasks['reviewTasks'])) {
        echo "Test case 5.5 passed: 'reviewTasks' key has an array as its value.\n";
        echo('<br>');
    } else {
        echo "Test case 5.5 failed: 'reviewTasks' key does not have an array as its value.\n";
        echo('<br>');
    }

    // Assert that the value of the 'doneTasks' key is an array
    assert(is_array($projectTasks['doneTasks']), "'doneTasks' key should have an array as its value");
    if (is_array($projectTasks['doneTasks'])) {
        echo "Test case 6.6 passed: 'doneTasks' key has an array as its value.\n";
        echo('<br>');
    } else {
        echo "Test case 6.6 failed: 'doneTasks' key does not have an array as its value.\n";
        echo('<br>');
    }

}

function test_get_project_tasks_with_empty_arguments(){

    echo('<br>');
    echo('<br>');
    echo("Test Case 2: Test with empty arguments");
    echo('<br>======================================================');
    echo('<br>');

    $project = new ProjectController();

    $args = array();
    $user_id = null;
    $projectTasks = $project->getProjectTasks($args, $user_id);

    if(assert($projectTasks === array())){
        echo('Tese case 2 passed');
        echo('<br>');
    }else{
        echo('tese case 2 failed');
        echo('<br>');
    }
}

function test_get_project_tasks_with_invalid_arguments(){

    echo('<br>');
    echo('<br>');
    echo("Test Case 3: Test with invalid arguments");
    echo('<br>======================================================');
    echo('<br>');

    $project = new ProjectController();

    $args = array(
        "project_id" => "4",
        "task_type" => "invalid"
    );
    $user_id = 1;
    $projectTasks = $project->getProjectTasks($args, $user_id);

    if(assert($projectTasks === array())){
        echo('Tese case 3 passed');
        echo('<br>');
    }else{
        echo('tese case 3 failed');
        echo('<br>');
    }
}
function test_get_project_tasks_with_invalid_user(){

    echo('<br>');
    echo('<br>');
    echo("Test Case 4: Test with invalid user_id");
    echo('<br>======================================================');
    echo('<br>');

    $project = new ProjectController();

    $args = array(
        "project_id" => 4,
        "task_type" => "project"
    );
    $user_id = "invalid";
    $projectTasks = $project->getProjectTasks($args, $user_id);

    if(assert($projectTasks === array())){
        echo('Tese case 4 passed');
        echo('<br>');
    }else{
        echo('tese case 4 failed');
        echo('<br>');
    }
}
test_get_project_tasks_with_valid_arguments();
test_get_project_tasks_with_empty_arguments();
test_get_project_tasks_with_invalid_arguments();
test_get_project_tasks_with_invalid_user();
