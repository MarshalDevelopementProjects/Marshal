<?php

namespace Tests\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Conference;
use Tests\Tester;

class TestConference extends Tester
{

    private Conference $conference;

    public function __construct()
    {
        parent::__construct(Conference::class);
    }

    function testScheduleConferenceWithEmptyArgs(): void
    {
        $this->assertFalse($this->conference->scheduleConference(args: []));
    }

    function testScheduleConferenceWithValidArgs(): void
    {
        $this->assertTrue($this->conference->scheduleConference(args: [
            "conf_name" => "New meeting",
            "conf_description" => "Final presentation discussion meeting",
            "project_id" => 5,
            "leader_id" => 5,
            "client_id" => 6,
            "on" => "2023-05-22",
            "at" => "20:46:00"
        ]));
    }

    function testScheduleConferenceWithInvalidNumberOfArgs(): void
    {
        // no conf_description
        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_name" => "New meeting",
                    "project_id" => 5,
                    "leader_id" => 5,
                    "client_id" => 6,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );

        // no conf_name
        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_description" => "Final presentation discussion meeting",
                    "project_id" => 5,
                    "leader_id" => 5,
                    "client_id" => 6,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );
    }

    function testScheduleWithInvalidData(): void
    {
        // testing with non-existing project leaders and clients respectively
        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_name" => "New meeting",
                    "conf_description" => "Final presentation discussion meeting",
                    "project_id" => 5,
                    "leader_id" => 10000,
                    "client_id" => 6,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );

        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_name" => "New meeting",
                    "conf_description" => "Final presentation discussion meeting",
                    "project_id" => 5,
                    "leader_id" => 5,
                    "client_id" => 10000,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );

        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_name" => "New meeting",
                    "conf_description" => "Final presentation discussion meeting",
                    "project_id" => 5,
                    "leader_id" => 10000,
                    "client_id" => 10000,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );

        // testing with non-existing project ids(foreign key enforcement)
        $this->assertException(
            callback: Conference::class . "::scheduleConference",
            args: [
                "args" => [
                    "conf_name" => "New meeting",
                    "conf_description" => "Final presentation discussion meeting",
                    "project_id" => 100,
                    "leader_id" => 5,
                    "client_id" => 6,
                    "on" => "2023-05-22",
                    "at" => "20:46:00"
                ]
            ]
        );
    }

    function testGetScheduledConferencesWithEmptyArgs(): void
    {
       // empty id
        $this->assertFalse($this->conference->getScheduledConferences(id: "", role: "LEADER"));

        // empty role
        $this->assertFalse($this->conference->getScheduledConferences(id: "5", role: ""));

        // both empty
        $this->assertFalse($this->conference->getScheduledConferences(id: "", role: ""));
    }

    function testGetScheduledConferencesWithInvalidArgs(): void
    {
        // invalid project id
        $this->assertTrue($this->conference->getScheduledConferences("100", "LEADER")); // the query must run
        $this->assertTrue(empty($this->conference->getConferenceData()));
    }

    function testGetScheduledConferencesWithValidArgs(): void
    {
        // from client perspective
        $this->conference->getScheduledConferences(id: "9", role: "CLIENT");

        // from the leaders perspective
        $this->conference->getScheduledConferences(id: "5", role: "LEADER");
    }

    function testGetDetailsOfConferenceWithEmptyArgs(): void
    {
        // empty conf_id
        $this->assertFalse($this->conference->getDetailsOfConference(conf_id: "")); // query must run
    }

    function testGetConferenceDetailsWithInvalidArgs(): void
    {
        // invalid conf_id
        $this->assertTrue($this->conference->getDetailsOfConference(conf_id: 100));
        $this->assertTrue(empty($this->conference->getConferenceData()));
    }

    function testGetConferenceDetailsWithValidArgs(): void
    {
        // valid conf_id
        $this->assertTrue($this->conference->getDetailsOfConference(conf_id: 2));
        $this->assertTrue(array_diff((array)$this->conference->getConferenceData(), [
                "conf_id" => 2,
                "conf_name" => "New Schedule",
                "project_id" => 5,
                "leader_id" => 5,
                "client_id" => 9,
                "on" => "2023-05-17",
                "at" => "19:38:00",
                "status" => "DONE",
                "conf_description" => "New "
            ]
        ));
    }

    function testGetScheduledConferencesByProjectWithEmptyArgs(): void
    {
        // with all the argument as empty
        $this->assertFalse($this->conference->getScheduledConferencesByProject(id: "", project_id: "", role: ""));

        // with empty user_id (id)
        $this->assertFalse($this->conference->getScheduledConferencesByProject(id: "", project_id: "5", role: "LEADER"));

        // with empty project_id
        $this->assertFalse($this->conference->getScheduledConferencesByProject(id: "5", project_id: "", role: "LEADER"));

        // with empty role
        $this->assertFalse($this->conference->getScheduledConferencesByProject(id: "5", project_id: "5", role: ""));
    }

    function testGetScheduledConferencesByProjectWithInvalidArgs(): void
    {
        // non-existing project leader id (id)
        $this->assertTrue($this->conference->getScheduledConferencesByProject(id: "100", project_id: "5", role: "LEADER")); // the query must run
        $this->assertTrue(empty($this->conference->getConferenceData()));

        // non-existing client id (id)
        $this->assertTrue($this->conference->getScheduledConferencesByProject(id: "10000", project_id: "5", role: "CLIENT")); // the query must run
        $this->assertTrue(empty($this->conference->getConferenceData()));

        // non-authorized user role
        $this->assertFalse($this->conference->getScheduledConferencesByProject(id: "5", project_id: "5", role: "MEMBER")); // the query must run
    }

    function testGetScheduledConferenceByProjectValidArgs(): void
    {
        // from the project leader perspective
        $this->assertTrue($this->conference->getScheduledConferencesByProject(id: "5", project_id: "5", role: "LEADER")); // the query must run
        $this->assertTrue(!empty($this->conference->getConferenceData()));

        // from the client perspective
        $this->assertTrue($this->conference->getScheduledConferencesByProject(id: "9", project_id: "5", role: "CLIENT")); // the query must run
        $this->assertTrue(!empty($this->conference->getConferenceData()));

    }

    public function setup(): void
    {
        $this->conference = new Conference();
    }

    public function run(): void
    {
        $this->setup();

        $this->testScheduleConferenceWithEmptyArgs();
        $this->testScheduleConferenceWithValidArgs();
        $this->testScheduleConferenceWithInvalidNumberOfArgs();
        $this->testScheduleWithInvalidData();
        $this->testGetScheduledConferencesWithEmptyArgs();
        $this->testGetScheduledConferencesWithInvalidArgs();
        $this->testGetScheduledConferencesWithValidArgs();
        $this->testGetDetailsOfConferenceWithEmptyArgs();
        $this->testGetConferenceDetailsWithInvalidArgs();
        $this->testGetConferenceDetailsWithValidArgs();
        $this->testGetScheduledConferencesByProjectWithEmptyArgs();
        $this->testGetScheduledConferencesByProjectWithInvalidArgs();
        $this->testGetScheduledConferenceByProjectValidArgs();

        $this->summary();
    }
}