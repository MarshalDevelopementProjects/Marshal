<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\GroupMember;


function testGetGroupData() {
    $group_member = new GroupMember(1, 1);
    echo "<pre>";
    echo "Testing the getGroupData() method\n";
    echo "<br>";
    var_dump($group_member->getGroupData());
    echo "</pre>";
}

function testSaveGroupMessage() {
    $group_member = new GroupMember(1, 1);
    echo "<pre>";
    echo "Testing the saveGroupMessage() method\n";
    $group_member->saveGroupMessage(1, 1, 1, "Hello my fellow group members");
    echo "<br>";
    echo "Passed the test for saveGroupMessages()";
    echo "</pre>";
}

function testGetGroupMessages() {
    $group_member = new GroupMember(1, 1);
    echo "<pre>";
    echo "Testing the getGroupMessages() method\n";
    echo "<br>";
    $group_member->getGroupForumMessages(1, 1);
    var_dump($group_member->getMessageData());
    echo "</pre>";
}

testGetGroupData();
testSaveGroupMessage();
testGetGroupMessages();