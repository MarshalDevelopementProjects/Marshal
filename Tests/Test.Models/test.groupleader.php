<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\GroupLeader;

function testGetGroupData() {
    $group_leader = new GroupLeader(1, 1);
    echo "<pre>";
    echo "Testing the getGroupData() method\n";
    echo "<br>";
    var_dump($group_leader->getGroupData());
    echo "</pre>";
}

function testSaveGroupMessage() {
    $group_leader = new GroupLeader(1, 1);
    echo "<pre>";
    echo "Testing the saveGroupMessage() method\n";
    $group_leader->saveGroupMessage(1, 1, "Hello my fellow group members");
    echo "<br>";
    echo "Passed the test for saveGroupMessages()";
    echo "</pre>";
}

function testGetGroupMessages() {
    $group_leader = new GroupLeader(1, 1);
    echo "<pre>";
    echo "Testing the getGroupMessages() method\n";
    echo "<br>";
    $group_leader->getGroupMessages(1, 1);
    var_dump($group_leader->getMessageData());
    echo "</pre>";
}

function testSaveGroupFeedbackMessage() {
    $group_leader = new GroupLeader(1, 1);
    echo "<pre>";
    echo "Testing the saveGroupFeedbackMessage() method\n";
    $group_leader->saveGroupFeedbackMessage(1, 1,  "Hello project leader");
    echo "<br>";
    echo "Passed the test for saveGroupFeedbackMessage()";
    echo "</pre>";
}

function testGetGroupFeedbackMessages() {
    $group_leader = new GroupLeader(1, 1);
    echo "<pre>";
    echo "Testing the getGroupFeedbackMessages() method\n";
    echo "<br>";
    $group_leader->getGroupFeedbackMessages(1);
    var_dump($group_leader->getMessageData());
    echo "</pre>";
}

testGetGroupData();
testSaveGroupMessage();
testSaveGroupFeedbackMessage();
testGetGroupMessages();

