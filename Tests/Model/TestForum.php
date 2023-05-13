<?php

namespace Tests\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Forum;
use Ratchet\App;
use Tests\Tester;

class TestForum extends Tester
{

    private Forum $forum;

    public function __construct()
    {
        parent::__construct(Forum::class);
    }

    protected function setup(): void
    {
        $this->forum = new Forum();
    }

    function testSaveForumMessageWithEmptyArgs(): void
    {
        // all three arguments empty
        $this->assertFalse($this->forum->saveForumMessage(sender_id: "", project_id: "", msg: ""));

        // sender_id empty
        $this->assertFalse($this->forum->saveForumMessage(sender_id: "", project_id: "5", msg: "Hello world"));

        // project_id empty
        $this->assertFalse($this->forum->saveForumMessage(sender_id: "8", project_id: "", msg: "Hello world"));

        // msg empty
        $this->assertFalse($this->forum->saveForumMessage(sender_id: "8", project_id: "5", msg: ""));
    }

    function testSaveForumMessageWithValidArgs(): void
    {
        $this->assertTrue($this->forum->saveForumMessage(sender_id: "8", project_id: "5", msg: "Hi how are you all?_"));
        $this->assertTrue($this->forum->saveForumMessage(sender_id: "6", project_id: "5", msg: "Hi how are you all?_"));
    }

    function testSaveForumMessageWithInvalidArgs(): void
    {
        // non-existing sender_id => foreign key violation
       $this->assertException(
           callback: Forum::class . "::saveForumMessage",
           args: [
               "sender_id" => 20,
               "project_id" => 5,
               "msg" => "Hello"
           ]
       );

        // non-existing project_id => foreign key violation
        $this->assertException(
            callback: Forum::class . "::saveForumMessage",
            args: [
                "sender_id" => 6,
                "project_id" => 500,
                "msg" => "Hello"
            ]
        );

        // invalid empty message case was checked in the function => testSaveForumMessageWithEmptyArgs
    }

    function testGetForumMessageWithEmptyArgs(): void
    {
        // empty project_id
        $this->assertFalse($this->forum->getForumMessages(project_id: ""));
    }

    function testGetForumMessageWithInvalidArgs(): void
    {
        // non-existing project id => foreign key violation
        $this->assertTrue($this->forum->getForumMessages(project_id: 100));
        $this->assertTrue(empty($this->forum->getMessageData()));

        $this->assertTrue($this->forum->getForumMessages(project_id: 190));
        $this->assertTrue(empty($this->forum->getMessageData()));

        $this->assertTrue($this->forum->getForumMessages(project_id: "100"));
        $this->assertTrue(empty($this->forum->getMessageData()));

        $this->assertTrue($this->forum->getForumMessages(project_id: "190"));
        $this->assertTrue(empty($this->forum->getMessageData()));
    }

    function testGetForumMessageWithValidArgs(): void
    {
        $this->assertTrue($this->forum->getForumMessages(project_id: 5)); // query must run
        $this->assertTrue(!empty($this->forum->getMessageData()));
    }

    function testSaveGroupFeedbackMessageWithEmptyArgs(): void
    {
        // all arguments are empty
        $this->assertFalse($this->forum->saveGroupFeedbackMessage(sender_id: "", project_id: "", group_id: "", msg: ""));

        // sender_id empty
        $this->assertFalse($this->forum->saveGroupFeedbackMessage(sender_id: "", project_id: "5", group_id: "4", msg: "Hello"));

        // project_id empty
        $this->assertFalse($this->forum->saveGroupFeedbackMessage(sender_id: "6", project_id: "", group_id: "4", msg: "New message"));

        // group_id empty
        $this->assertFalse($this->forum->saveGroupFeedbackMessage(sender_id: "6", project_id: "5", group_id: "", msg: "Hello world"));

        // msg empty
        $this->assertFalse($this->forum->saveGroupFeedbackMessage(sender_id: "6", project_id: "5", group_id: "4", msg: ""));
    }

    function testSaveGroupFeedbackMessageWithInvalidArgs(): void
    {
        // with non-existing sender_id
        $this->assertException(
            callback: Forum::class . "::saveGroupFeedbackMessage",
            args: [
                "sender_id" => "10",
                "project_id" => "5",
                "group_id" => "4",
                "msg" => "New message"
            ]
        );

        // with non-existing project_id
        $this->assertException(
            callback: Forum::class . "::saveGroupFeedbackMessage",
            args: [
                "sender_id" => "6",
                "project_id" => "10",
                "group_id" => "4",
                "msg" => "New message"
            ]
        );

        // with non-existing group_id
        $this->assertException(
            callback: Forum::class . "::saveGroupFeedbackMessage",
            args: [
                "sender_id" => "5",
                "project_id" => "5",
                "group_id" => "10",
                "msg" => "New message"
            ]
        );

        // empty message was tested in an above test function
    }

    function testSaveGroupFeedbackMessageWithValidArgs(): void
    {
        $this->assertTrue($this->forum->saveGroupFeedbackMessage(
            "5",
                "5",
                 "4",
                "New message"
            )
        );

        $this->assertTrue($this->forum->saveGroupFeedbackMessage(
            "6",
            "5",
            "4",
            "New message"
            )
        );

        $this->assertTrue($this->forum->saveGroupFeedbackMessage(
            "6",
            "5",
            "4",
            "New message _ 1"
            )
        );

        $this->assertTrue($this->forum->saveGroupFeedbackMessage(
            "5",
            "5",
            "4",
            "New message __ 2"
            )
        );
    }

    function testGetGroupFeedbackMessageWithEmptyArgs(): void
    {
        // empty project_id and group_id
        $this->assertFalse($this->forum->getGroupFeedbackMessages(project_id: "", group_id: ""));

        // empty project_id
        $this->assertFalse($this->forum->getGroupFeedbackMessages(project_id: "", group_id: "4"));

        // empty group_id
        $this->assertFalse($this->forum->getGroupFeedbackMessages(project_id: "5", group_id: ""));
    }

    function testGetGroupFeedbackMessageWithInvalidArgs(): void
    {
        // invalid project_id
        $this->assertTrue($this->forum->getGroupFeedbackMessages(project_id: "100", group_id: "4")); // query must run
        $this->assertTrue(empty($this->forum->getMessageData()));

        // invalid group_id
        $this->assertTrue($this->forum->getGroupFeedbackMessages(project_id: "5", group_id: "100")); // query must run
        $this->assertTrue(empty($this->forum->getMessageData()));
    }

    function testGetGroupFeedbackMessageWithValidArgs(): void
    {
        // need to have some data in the group_feedback_message table regarding this project and the group
        // otherwise the assertion will fail
        $this->assertTrue($this->forum->getGroupFeedbackMessages(project_id: 5, group_id: 4));
        $this->assertTrue(!empty($this->forum->getMessageData()));
    }

    public function run(): void
    {
        $this->setup();

        $this->testSaveForumMessageWithEmptyArgs();
        $this->testSaveForumMessageWithValidArgs();
        $this->testSaveForumMessageWithInvalidArgs();
        $this->testGetForumMessageWithEmptyArgs();
        $this->testGetForumMessageWithInvalidArgs();
        $this->testGetForumMessageWithValidArgs();

        $this->testSaveGroupFeedbackMessageWithEmptyArgs();
        $this->testSaveGroupFeedbackMessageWithInvalidArgs();
        $this->testSaveGroupFeedbackMessageWithValidArgs();

        $this->testGetGroupFeedbackMessageWithEmptyArgs();
        $this->testGetGroupFeedbackMessageWithInvalidArgs();
        $this->testGetGroupFeedbackMessageWithValidArgs();

        $this->summary();
    }
}