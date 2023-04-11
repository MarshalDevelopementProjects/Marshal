<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\ProjectLeader;

function testGetProjectData()
{
    $project_leader = new ProjectLeader(1);
    echo "<pre>";
    echo "Testing the getProjectData() method\n";
    echo "<br>";
    var_dump($project_leader->getProjectData());
    echo "</pre>";
    echo "<pre>";
}

function testSaveProjectFeedbackMessage()
{
    echo "<pre>";
    echo "Testing the saveProjectFeedbackMessage() method\n";
    echo "<br>";
    $project_leader = new ProjectLeader(1);
    $project_leader->saveProjectFeedbackMessage(1, 1, "Hello Client");
    echo "Passed the test\n";
    echo "</pre>";
}

function testGetProjectFeedbackMessages()
{
    $project_leader = new ProjectLeader(1);
    $project_leader->getProjectFeedbackMessages(1);
    echo "<pre>";
    echo "Testing the getProjectFeedbackMessages() method\n";
    echo "<br>";
    var_dump($project_leader->getMessageData());
    echo "</pre>";
}

function testSaveForumMessage()
{
    echo "<pre>";
    echo "Testing the saveForumMessage() method\n";
    echo "<br>";
    $project_leader = new ProjectLeader(1);
    $project_leader->saveForumMessage(1, 1, "Hello project members");
    echo "Passed the test\n";
    echo "</pre>";
}

function testGetFroumMessages()
{
    $project_leader = new ProjectLeader(1);
    $project_leader->getForumMessages(1);
    echo "<pre>";
    echo "Testing the getForumMessages() method\n";
    echo "<br>";
    var_dump($project_leader->getMessageData());
    echo "</pre>";
}

function testSaveGroupFeedbackMessage()
{
    echo "<pre>";
    echo "Testing the saveProjectFeedbackMessage() method\n";
    echo "<br>";
    $project_leader = new ProjectLeader(1);
    $project_leader->saveGroupFeedbackMessage(1, 1, 1, "Hello group leader");
    echo "Passed the test case\n";
    echo "</pre>";
}

function testGetGroupFeedbackMessages()
{
    echo "<pre>";
    echo "Testing the getGroupFeedbackMessages() method\n";
    echo "<br>";
    $project_leader = new ProjectLeader(1);
    $project_leader->getGroupFeedbackMessages(1, 1);
    echo "<br>";
    var_dump($project_leader->getMessageData());
    echo "</pre>";
}

function testGetMessageData()
{
    echo "<pre>";
    echo "Testing the trivial method for retrieving message data getMessageData() method\n";
    echo "<br>";
    $project_leader = new ProjectLeader(1);
    echo "<br>";
    var_dump($project_leader->getMessageData());
    echo "</pre>";
}

testGetProjectData();

testSaveProjectFeedbackMessage();
testGetProjectFeedbackMessages();

testSaveForumMessage();
testGetFroumMessages();

testSaveGroupFeedbackMessage();
testGetGroupFeedbackMessages();


