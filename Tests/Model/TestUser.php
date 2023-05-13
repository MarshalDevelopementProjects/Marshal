<?php

namespace Tests\Model;

use App\Model\User;
use Tests\Tester;

require __DIR__ . '/../../vendor/autoload.php';

class TestUser extends Tester
{

    private User $user;

    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function setup(): void
    {
        $this->user = new User();
    }

    public function testCreateUserWithEmptyArgs(): void
    {
        $this->assertFalse($this->user->createUser(args: []));
    }

    public function testCreateUserWithMissingArguments(): void
    {
        $this->assertException(
            callback: User::class . '::createUser',
            args: [
                "args" => ["username" => "kylo_ren"]
            ],
            message: ""
        );
    }

    // test update user status function
    public function testUpdateStateWithValidArguments(): void
    {
        $this->assertTrue($this->user->updateState(1, 'OFFLINE'));
    }

    public function testUpdateStateWithEmptyArguments(): void
    {
        $this->assertFalse($this->user->updateState(id: "", user_state: ""));
        $this->assertFalse($this->user->updateState(id: "1", user_state: ""));
        $this->assertFalse($this->user->updateState(id: "", user_state: "ONLINE"));
    }

    // test update user password function
    public function testUpdatePasswordWithValidArguments(): void
    {
        $this->assertTrue($this->user->updatePassword(id: 1, new_password: '1234567890'));
    }

    public function testUpdatePasswordWithEmptyArguments(): void
    {
        $this->assertFalse($this->user->updatePassword(id: "", new_password: ""));
        $this->assertFalse($this->user->updatePassword(id: "1", new_password: ""));
        $this->assertFalse($this->user->updatePassword(id: 1, new_password: ''));
    }

    // test read user functionality
    public function testReadUserWithValidArguments(): void
    {
        $this->assertTrue($this->user->readUser(key: 'id', value: 5));
        $this->assertTrue($this->user->readUser(key: 'username', value: 'Harsha_123'));
    }
    public function testReadUserWithInvalidArguments(): void
    {
        $this->assertFalse($this->user->readUser(key: 'id', value: 10000));
    }
    public function testReadUserWithEmptyArguments(): void
    {
        $this->assertFalse($this->user->readUser(key: "", value: ""));
        $this->assertFalse($this->user->readUser(key: "", value: "value"));
    }

    // test get all users function
    public function testGetAllUsersWithValidArguments(): void
    {
        $this->assertTrue(is_array($this->user->getAllUsers(
            array(
                'user_state' => 'ONLINE'
            ),
            'WHERE user_state = :user_state'
        )));
    }
    public function testGetAllUsersWithInvalidArguments(): void
    {
        $this->assertTrue(empty($this->user->getAllUsers(
            array(
                'user_state' => 'ONGOING'
            ),
            'WHERE user_state = :user_state'
        )));
    }
    public function testGetAllUsersWithEmptyArguments(): void
    {
        $this->assertTrue(empty($this->user->getAllUsers([], "")));
    }

    // test update user profile functionality
    public function testUpdateProfilePictureWithValidArguments(): void
    {
        $this->assertTrue($this->user->updateProfilePicture(2, '/App/Database/Uploads/ProfilePictures/unvicio_squab_sun_glasses_aviator_under_a_shower_of_bubbles_Fra_c90eabc9-2980-4f3d-91f4-efe65adafcdb.png'));
    }

    public function testUpdateProfilePictureWithEmptyArguments(): void
    {
        $this->assertFalse($this->user->updateProfilePicture(id: "", value: ""));
        $this->assertFalse($this->user->updateProfilePicture(id: "1", value: ""));
        $this->assertFalse($this->user->updateProfilePicture(id: "", value: "/App/Database/Uploads/ProfilePictures/unvicio_squab_sun_glasses_aviator_under_a_shower_of_bubbles_Fra_c90eabc9-2980-4f3d-91f4-efe65adafcdb.png"));
    }

    // test update user functionality
    public function testUpdateUserWithValidArguments(): void
    {
        $this->assertTrue($this->user->updateUser(1, array(
            "username" => "test user name",
            "first_name" => "test first name",
            "last_name" => "test last_name",
            "email_address" => "test email_address",
            "phone_number" => '0071223432',
            "position" => "Developer",
            "bio" => "test bio",
            "user_status" => "ONLINE"
        )));
    }


    public function testUpdateUserWithMissingArguments(): void
    {
        $this->assertFalse($this->user->updateUser(id: "", args: []));
        $this->assertFalse($this->user->updateUser(id: "5", args: []));
        $this->assertFalse($this->user->updateUser(id: "", args: [
            "username" => "test user name",
            "first_name" => "test first name",
            "last_name" => "test last_name",
            "email_address" => "test email_address",
            "phone_number" => '0071223432',
            "position" => "Developer",
            "bio" => "test bio",
            "user_status" => "ONLINE"
        ]));
    }
    public function testUpdateUserWithMissingAttributes(): void
    {
        $this->assertException(
            callback: User::class . '::updateUser',
            args: [
                'id' => 1,
                'args' => array(
                    "username" => "test user name",
                    "first_name" => "test first name",
                    "last_name" => "test last_name",
                    "email_address" => "test email_address",
                    "phone_number" => '0071223432'
                )
            ],
            message: ""
        );
    }

    // test isUserJoinedProject function
    public function testIsUserJoinedProject()
    {

        // test with valid arguments
        $this->assertTrue($this->user->isUserJoinedToProject(array(
            "project_id" => 1,
            "member_id" => 1
        )));

        // test with empty arguments
        $this->assertException(
            callback: User::class . '::isUserJoinedToProject',
            args: [],
            message: ""
        );

        // test with missing arguments
        $this->assertException(
            callback: User::class . '::isUserJoinedToProject',
            args: [
                'args' => [
                    "project_id" => 1
                ]
            ],
            message: ""
        );
    }

    public function testGetCommit(): void
    {
        // test with invalid id
        $this->assertFalse($this->user->getCommit(1000));

        // test with empty id
        $this->assertException(
            callback: User::class . '::getCommit',
            args: ["id" => ""],
            message: ""
        );
    }

    public function run(): void
    {
        // TODO: Implement run() method.
        $this->setup();

        $this->testCreateUserWithEmptyArgs();
        $this->testCreateUserWithMissingArguments();

        $this->testUpdateStateWithValidArguments();
        $this->testUpdateStateWithEmptyArguments();

        $this->testUpdatePasswordWithValidArguments();
        $this->testUpdatePasswordWithEmptyArguments();

        $this->testReadUserWithValidArguments();
        $this->testReadUserWithEmptyArguments();
        $this->testReadUserWithInvalidArguments();

        $this->testGetAllUsersWithValidArguments();
        $this->testGetAllUsersWithInvalidArguments();
        $this->testReadUserWithEmptyArguments();

        $this->testUpdateProfilePictureWithValidArguments();
        $this->testUpdateProfilePictureWithEmptyArguments();

        $this->testUpdateUserWithValidArguments();
        $this->testUpdateUserWithMissingArguments();
        $this->testUpdateUserWithMissingAttributes();

        $this->testisuserjoinedproject();

        $this->testgetcommit();

        $this->summary();
    }
}
