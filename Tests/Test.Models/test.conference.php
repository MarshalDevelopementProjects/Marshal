<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Conference;

// The output will be JSON
function testGetScheduledConferences(): void
{
    $conference = new Conference();
    $conference->getScheduledConferences(1, "LEADER"); // To test the leader queries try this
    echo "<pre>";
    echo "Testing the getScheduledConferences() method with the LEADER initiator\n";
    echo "<br>";
    var_dump($conference->getConferenceData());
    // =================================================================================================
    echo "Testing the getScheduledConferences() method with the CLIENT initiator\n";
    $conference->getScheduledConferences(2, "CLIENT"); // To test the client queries try this
    var_dump($conference->getConferenceData());
    echo "</pre>";
}

function testScheduleConference(): void
{
    $conference = new Conference();
    $test_data_pass_cases = ["conf_name" => "New feature discussion meeting", "project_id" => 2, "leader_id" => 2, "client_id" => 1, "on" => "2023-05-02", "at" => "20:46:00"];
    echo "<pre>";
    echo "Testing the scheduleConference() method\n";
    echo "<br>";
    if($conference->scheduleConference($test_data_pass_cases)) {
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        $conference->getScheduledConferences(1, "LEADER"); // To test the leader queries try this
        echo "<pre>";
        echo "Testing the getScheduledConferences() method with the LEADER initiator\n";
        echo "<br>";
        var_dump($conference->getConferenceData());
        echo "<br>";
    } else {
        printf("%s\n", "The function failed");
    }
}

function testChangeStatusOfConference(): void
{
    $conference = new Conference();
    $returned = $conference->changeStatusOfConference(conf_id: 3, status: "DONE");
    echo "<pre>";
    if($returned) {
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        echo "Testing the changeStatusOfConference() method -> 1st change\n";
        $conference->getDetailsOfConference(conf_id: 3);
        echo "<br>";
    } else {
        echo "<br>";
        print_r("Function failed to executed");
        print_r("$returned");
    }

    $returned = $conference->changeStatusOfConference(conf_id: 3, status: "CANCELLED");
    if($returned) {
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        echo "Testing the changeStatusOfConference() method -> 2nd change\n";
        $conference->getDetailsOfConference(conf_id: 3);
        echo "<br>";
    } else {
        echo "<br>";
        print_r("Function failed to executed -> following is the returned value :: ");
        print_r("$returned");
    }

    // THE MODEL DOES RESTRICT ANY CHANGES TO A CANCELLED STATE
    $returned = $conference->changeStatusOfConference(conf_id: 3, status: "DONE");
    if($returned) {
        printf("%s\n", "The function completed successfully->checking the values");
        echo "<br>";
        $conference->getDetailsOfConference(conf_id: 3);
        echo "Testing the changeStatusOfConference() method -> 3nd change\n";
        echo "<br>";
    } else {
        echo "<br>";
        print_r("$returned");
    }
    echo "</pre>";
}


// testGetScheduledConferences();
// testScheduleConference();
// testChangeStatusOfConference();
