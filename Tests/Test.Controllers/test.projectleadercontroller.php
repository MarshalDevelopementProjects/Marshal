<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\ProjectLeader\ProjectLeaderController;

// before you run the test cases please make sure that you are logged in
// otherwise you cannot execute the test cases
// and make sure that you are logged in as kylo_ren when testing

function testPostMessageToProjectForum(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->postMessageToProjectForum(["message" => "Hello fellow project members, how you all doing?"]);
}

function testGetProjectForumMessages(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->getProjectForumMessages();
}

function testPostMessageToProjectFeedback(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->postMessageToProjectFeedback(["message" => "Hello client how is your life?"]);
}

function testGetProjectFeedbackMessages(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->getProjectFeedbackMessages();
}

function testScheduleConference() {
    $project_leader_controller = new ProjectLeaderController();
    echo "<pre>";
    $project_leader_controller->scheduleConference([]);
    echo "</pre>";
}

// the following two functions can only be tested much later
/*function testPostMessageToGroupFeedback(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->postMessageToGroupFeedback(["message" => "Hello client how is your life?"]);
}*/

/*function testGetGroupFeedbackMessages(): void
{
    $project_leader_controller = new ProjectLeaderController();
    $project_leader_controller->getGroupFeedbackMessages();
}*/

// uncomment a single test at a time

// testPostMessageToProjectForum();
// testGetProjectForumMessages();
// testPostMessageToProjectFeedback();
// testGetProjectFeedbackMessages();
// testPostMessageToGroupFeedback();
// testGetGroupFeedbackMessages();

testScheduleConference();