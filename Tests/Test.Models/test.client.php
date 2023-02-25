<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Client;


function testGetProjectData(): void
{
    $client = new Client(1);
    echo "<pre>";
    echo "Testing the getProjectData() method\n";
    echo "<br>";
    var_dump($client->getProjectData());
    echo "</pre>";
    echo "<pre>";
}

function testSaveProjectFeedbackMessage(): void
{
    echo "<pre>";
    echo "Testing the saveProjectFeedbackMessage() method\n";
    echo "<br>";
    $client = new Client(1);
    $client->saveProjectFeedbackMessage(1,  "Hello Project leaders");
    echo "Passed the test\n";
    echo "</pre>";
}

function testGetProjectFeedbackMessages(): void
{
    $client = new Client(1);
    $client->getProjectFeedbackMessages();
    echo "<pre>";
    echo "Testing the getProjectFeedbackMessages() method\n";
    echo "<br>";
    var_dump($client->getMessageData());
    echo "</pre>";
}

testGetProjectFeedbackMessages();
testSaveProjectFeedbackMessage();

testGetProjectFeedbackMessages();
testGetProjectData();
