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

    public function testCreateUserWithMissingArguments():void
    {
        $this->assertException(
            callback: User::class . '::createUser',
            args: [
                "args" => [
                    "username" => "kylo_ren"
                ]
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
        $this->assertException(
            callback: User::class . '::updateState',
            args: null,
            message: ""
        );
    }
    public function testUpdateStateWithMissingArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updateState',
            args: [
                "id" => 1
            ],
            message: ""
        );
    }

    // test update user password function
    public function testUpdatePasswordWithValidArguments(): void
    {
        $this->assertTrue($this->user->updatePassword(1, 'newpassword'));
    }

    public function testUpdatePasswordWithEmptyArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updatePassword',
            args: null,
            message: ""
        );
    }
    public function testUpdatePasswordWithMissingArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updatePassword',
            args: [
                "id" => 1
            ],
            message: ""
        );
    }

    // test read user functionality
    public function testReadUserWithValidArguments() :void
    {
        $this->assertTrue($this->user->readUser('id', 1));
    }
    public function testReadUserWithInvalidArguments() :void
    {
        $this->assertFalse($this->user->readUser('id', 10000));
    }
    public function testReadUserWithEmptyArguments() :void
    {
        $this->assertException(
            callback: User::class . '::readUser',
            args: [],
            message: ""
        );   
    }
    public function testReadUserWithMissingArguments(): void
    {
        $this->assertException(
            callback: User::class . '::readUser',
            args: [
                'key' => 'id'
            ],
            message: ""
        );  
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
        $this->assertTrue(empty(is_array($this->user->getAllUsers(
            array(
                'user_state' => 'ONGOING'
            ),
            'WHERE user_state = :user_state'
        ))));
    }
    public function testGetAllUsersWithEmptyArguments(): void
    {
        $this->assertTrue(empty(is_array($this->user->getAllUsers(null, null))));
    }

    // test update user profile functionality
    public function testUpdateProfilePictureWithValidArguments(): void
    {
        $this->assertTrue($this->user->updateProfilePicture(2, '/App/Database/Uploads/ProfilePictures/unvicio_squab_sun_glasses_aviator_under_a_shower_of_bubbles_Fra_c90eabc9-2980-4f3d-91f4-efe65adafcdb.png'));
    }

    public function testUpdateProfilePictureWithEmptyArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updateProfilePicture',
            args: [],
            message: ""
        );  
    }
    public function testUpdateProfilePictureWithMissingArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updateProfilePicture',
            args: [
                'id' => 1
            ],
            message: ""
        );  
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
    public function testUpdateUserWithEmptyArguments(): void
    {
        $this->assertException(
            callback: User::class . '::updateUser',
            args: [],
            message: ""
        ); 
    }
    public function testUpdateUserWithMissingArguments(): void
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

    // test check user role functionality    
    public function testCheckUserRole()
    {   
        // Test for valid project leader
        $this->assertTrue($this->user->checkUserRole(1, 'LEADER', 'PROJECT'));
        
        // Test for invalid project leader
        $this->assertFalse($this->user->checkUserRole(456, 'LEADER', 'PROJECT'));
        
        // Test for valid project member
        $this->assertTrue($this->user->checkUserRole(2, 'MEMBER', 'PROJECT'));
        
        // Test for invalid project member
        $this->assertFalse($this->user->checkUserRole(1, 'MEMBER', 'PROJECT'));
        
        // Test for valid project client
        $this->assertTrue($this->user->checkUserRole(3, 'CLIENT', 'PROJECT'));
        
        // Test for invalid project client
        $this->assertFalse($this->user->checkUserRole(101112, 'CLIENT', 'PROJECT'));
        
        // Test for valid group leader
        $this->assertTrue($this->user->checkUserRole(1, 'LEADER', 'GROUP'));
        
        // Test for invalid group leader
        $this->assertFalse($this->user->checkUserRole(789, 'LEADER', 'GROUP'));
        
        // Test for valid group member
        $this->assertTrue($this->user->checkUserRole(3, 'MEMBER', 'GROUP'));
        
        // Test for invalid group member
        $this->assertFalse($this->user->checkUserRole(101112, 'MEMBER', 'GROUP'));
        
        // Test for invalid type
        $this->assertFalse($this->user->checkUserRole(123, 'LEADER', 'INVALID'));
    }

    // test isUserJoinedProject function
    public function testIsUserJoinedProject(){

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

    public function testgetCommit(): void
    {
        // test with valid id
        $this->assertTrue(is_array($this->user->getCommit(1)));

        // test with invalid id
        $this->assertFalse(is_array($this->user->getCommit(1000)));

        // test with emoty id
        $this->assertException(
            callback: User::class . '::getCommit',
            args: 1,
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
        $this->testUpdateStateWithMissingArguments();

        $this->testUpdatePasswordWithValidArguments();
        $this->testUpdatePasswordWithEmptyArguments();
        $this->testUpdatePasswordWithMissingArguments();

        $this->testReadUserWithValidArguments();
        $this->testReadUserWithEmptyArguments();
        $this->testReadUserWithMissingArguments();
        $this->testReadUserWithInvalidArguments();

        $this->testGetAllUsersWithValidArguments();
        $this->testGetAllUsersWithInvalidArguments();
        $this->testReadUserWithEmptyArguments();

        $this->testUpdateProfilePictureWithValidArguments();
        $this->testUpdateProfilePictureWithInvalidArguments();
        $this->testUpdateProfilePictureWithMissingArguments();
        $this->testUpdateProfilePictureWithEmptyArguments();

        $this->testUpdateUserWithValidArguments();
        $this->testUpdateUserWithMissingArguments();
        $this->testUpdateUserWithEmptyArguments();

        testCheckUserRole();

        testIsUserJoinedProject();

        testgetCommit();

        
        $this->summary();
    }
}