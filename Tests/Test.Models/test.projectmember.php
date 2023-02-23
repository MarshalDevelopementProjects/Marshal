<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\ProjectMember;


function testGetProjectData()
{
    $project_member = new ProjectMember(1);
    echo "<pre>";
    echo "Testing the getProjectData() method\n";
    echo "<br>";
    var_dump($project_member->getProjectData());
    echo "</pre>";
    echo "<pre>";
}

function testSaveForumMessage()
{
    echo "<pre>";
    echo "Testing the saveForumMessage() method\n";
    echo "<br>";
    $project_member = new ProjectMember(1);
    $project_member->saveForumMessage(1, 1, "Hello my fellow members");
    echo "Passed the test\n";
    echo "</pre>";
}

function testGetFroumMessages()
{
    $project_member = new ProjectMember(1);
    $project_member->getForumMessages(1);
    echo "<pre>";
    echo "Testing the getForumMessages() method\n";
    echo "<br>";
    var_dump($project_member->getMessageData());
    echo "</pre>";
}

testGetProjectData();
testGetFroumMessages();
testSaveForumMessage();
testGetFroumMessages();
