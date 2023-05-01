<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\Conference\ConferenceController;

function testScheduleConference(): void
{
    $conferenceController = new ConferenceController();

    /*$conferenceController->scheduleConference(
        [
        "conf_name" => "Project completion code review discussion meeting",
        "project_id" => 1,
        "leader_id" => 1,
        "client_id" => 2,
        "on" => "2023-04-29",
        "at" => "22:46:00"
        ]
    );*/

    $test_data_failure_cases = ["conf_name" => "New feature discussion meeting", "project_id" => 6, "leader_id" => 5, "client_id" => 5, "on" => "2022-05-02", "at" => "20:46:00"];
    $returned = $conferenceController->scheduleConference($test_data_failure_cases);
    if(is_bool($returned) && $returned) {
        echo "<pre>";
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        $conferenceController->getScheduledConferenceDetails(1, "LEADER"); // To test the leader queries try this
        echo "<pre>";
        echo "Testing the getScheduledConferences() method with the LEADER initiator\n";
        echo "<br>";
        var_dump($conferenceController->getScheduledConferenceDetails(1, "LEADER"));
        echo "<br>";
        echo "</pre>";
    } else {
        echo "<pre>";
        printf("%s\n", "The function failed, and here is why :: ");
        print_r($returned);
        echo "</pre>";
    }
}

function testChangeConferenceStatus() {
    $conferenceController = new ConferenceController();
    $args = ["conf_id" => 1, "status" => "OVERDUE"];
    $returned = $conferenceController->changeConferenceStatus($args);
    echo "<pre>";
    if(is_bool($returned) && $returned) {
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        $conferenceController->getScheduledConferenceDetails(1, "LEADER"); // To test the leader queries try this
        echo "<pre>";
        echo "Testing the getScheduledConferences() method with the LEADER initiator\n";
        echo "<br>";
        var_dump($conferenceController->getScheduledConferenceDetails(1, "LEADER"));
        echo "<br>";
    } else {
        printf("%s\n", "The function failed, and here is why :: ");
        echo "<br>";
        print_r($returned);
    }
    echo "</pre>";
}

function testGetScheduledConferenceDetailsByProject() {
    $conferenceController = new ConferenceController();
    echo "<pre>";
    var_dump($conferenceController->getScheduledConferenceDetailsByProject(1, 4,"LEADER")); // To test the leader queries try this
    echo "</pre>";
}

// testScheduleConference();
// testChangeConferenceStatus();
testGetScheduledConferenceDetailsByProject();