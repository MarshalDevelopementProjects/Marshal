<?php

namespace Tests\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Admin;
use Tests\Tester;

class TestAdmin extends Tester
{
    private Admin $admin;

    public function __construct()
    {
        parent::__construct(Admin::class);
    }

    public function testCreateAdmin()
    {
        // here we are testing a private function don't run this test without
        // making the function public in the Admin model

        // testing the createAdmin function with empty arguments
        $this->assertFalse($this->admin->createAdmin(args: []));

        // testing the createAdmin function with valid arguments
        $this->assertTrue(
            $this->admin->createAdmin(
                [
                    "username" => "ConfAdmin",
                    "first_name" => "Isaac",
                    "last_name" => "Newton",
                    "email_address" => "isaac_newton@gmail.com",
                    "password" => "1234567890",
                    "street_address" => "No. 10, Ward Place",
                    "city" => "Colombo",
                    "country" => "Sri Lanka",
                    "phone_number" => "0778502328"
                ]
            )
        );

        // testing the createAdmin function with invalid number of arguments
        $this->assertException(
            callback: Admin::class . "::createAdmin",
            args: [
                "args" =>  [
                    "username" => "NetAdmin",
                    "email_address" => "jack_sparrow@gmail.com",
                    "password" => "1234567890",
                    "street_address" => "No. 10, Ward Place",
                    "city" => "Colombo",
                    "country" => "Sri Lanka",
                    "phone_number" => "0778912324"
                ]
            ]
        );

        // testing the createAdmin function with no password
        $this->assertFalse($this->admin->createAdmin(
            [
                "username" => "NetAdmin",
                "first_name" => "Jack",
                "last_name" => "Sparrow",
                "email_address" => "jack_sparrow@gmail.com",
                "street_address" => "No. 10, Ward Place",
                "city" => "Colombo",
                "country" => "Sri Lanka",
                "phone_number" => "0778912324"
            ]
        ));
    }

    public function testReadAdminWithEmptyArguments(): void
    {
        $this->assertFalse($this->admin->readAdmin(key: "", value: ""));
        $this->assertFalse($this->admin->readAdmin(key: "id", value: ""));
        $this->assertFalse($this->admin->readAdmin(key: "", value: "1"));
    }

    public function testReadAdminWithValidArguments(): void
    {
        $this->assertTrue($this->admin->readAdmin(key: "username", value: "ConfAdmin"));
        $this->assertTrue($this->admin->readAdmin(key: "first_name", value: "Isaac"));
        $this->assertTrue($this->admin->readAdmin(key: "last_name", value: "Newton"));
    }

    public function testReadAdminWithInvalidArguments(): void
    {
        $this->assertFalse($this->admin->readAdmin(key: "last_name", value: "Hannah"));
        $this->assertFalse($this->admin->readAdmin(key: "first_name", value: "Levi"));
    }

    public function testReadAdminWithInvalidFieldNames(): void
    {
        $this->assertException(
            callback: Admin::class . "::readAdmin",
            args: [
                "key" => "invalid_field_name",
                "value" => "John"
            ]
        );
    }

    public function testCreateUserWithEmptyArgs(): void
    {
        $this->assertFalse($this->admin->createUser(args: []));
    }

    public function testCreateUserWithValidArguments(): void
    {
        $this->assertTrue($this->admin->createUser(
            args: [
                "username" => "kylo_ren",
                "first_name" => "Kylo",
                "last_name" => "Ren",
                "email_address" => "kylo_ren@gmail.com",
                "password" => "1234567890",
                "phone_number" => "0778916324"
            ]
        ));
    }

    function testCreateUserWithInvalidNumberOfArgs(): void
    {
        // no password case
        $this->assertFalse($this->admin->createUser(
            args: [
                "args" => [
                    "username" => "kylo_ren",
                    "first_name" => "Kylo",
                    "last_name" => "Ren",
                    "email_address" => "kylo_ren@gmail.com",
                ]
            ]
        ));

        // no username case
        $this->assertException(
            callback: Admin::class . "::createUser",
            args: [
                "args" => [
                    "first_name" => "Kylo",
                    "last_name" => "Ren",
                    "email_address" => "kylo_ren@gmail.com",
                    "password" => "1234567890"
                ]
            ]
        );

        // no first_name
        $this->assertException(
            callback: Admin::class . "::createUser",
            args: [
                "args" => [
                    "username" => "kylo_ren",
                    "last_name" => "Ren",
                    "email_address" => "kylo_ren@gmail.com",
                    "password" => "1234567890"
                ]
            ]
        );

        // no last_name
        $this->assertException(
            callback: Admin::class . "::createUser",
            args: [
                "args" => [
                    "username" => "kylo_ren",
                    "first_name" => "Ren",
                    "email_address" => "kylo_ren@gmail.com",
                    "password" => "1234567890"
                ]
            ]
        );
    }

    function testReadUserWithEmptyArgs(): void
    {
        // both key and value fields are empty
        $this->assertFalse($this->admin->readUser(key: "", value: ""));

        // empty value field
        $this->assertFalse($this->admin->readUser(key: "id", value: ""));

        // empty key field
        $this->assertFalse($this->admin->readUser(key: "", value: "8"));
    }

    function testReadUserWithValidArguments(): void
    {
        // using id
        $this->assertTrue($this->admin->readUser(key: "id", value: "8"));

        // using username
        $this->assertTrue($this->admin->readUser(key: "username", value: "Bhathiya_123"));

        // using first_name
        $this->assertTrue($this->admin->readUser(key: "first_name", value: "Bhathiya"));
    }

    function testReadUserWithInValidKeyField(): void
    {
        $this->assertException(
            callback: Admin::class . "::readUser",
            args: [
                "key" => "invalid_key_field",
                "value" => "Bhathiya_123"
            ]
        );
    }

    function testReadUserWithNonExistingValue(): void
    {
        // non-existing id
        $this->assertFalse($this->admin->readUser(key: "id", value: "4000"));

        // non-existing username
        $this->assertFalse($this->admin->readUser(key: "username", value: "edward_rixton"));

        // non-existing first_name
        $this->assertFalse($this->admin->readUser(key: "first_name", value: "Rixton"));
    }

    function testReadAllUsers(): void
    {
        // when there are no users in the database run this test
        // $this->assertFalse($this->admin->readAllUsers());

        // when there are users
        $this->assertTrue($this->admin->readAllUsers());
    }

    function testGetActiveUsers(): void
    {
        // when there are no active users run this query
        // otherwise this fails
        // $this->assertFalse($this->admin->getActiveUsers());

        // when there are active users run this function
        // otherwise this test will fail
        $this->assertTrue($this->admin->getActiveUsers());
    }

    function testDisableUserAccountWithEmptyArgs(): void
    {
        // empty key and value
        $this->assertFalse($this->admin->disableUserAccount(key: "", value: ""));

        // empty value
        $this->assertFalse($this->admin->disableUserAccount(key: "id", value: ""));

        // empty key
        $this->assertFalse($this->admin->disableUserAccount(key: "", value: "Bhathiya_123"));
    }

    function testDisableUserAccountWithValidArgs(): void
    {
        // using the id
        $this->assertTrue($this->admin->disableUserAccount(key: "id", value: "8"));

        // using the username
        $this->assertTrue($this->admin->disableUserAccount(key: "username", value: "Bhathiya_123"));
    }

    function testDisableUserAccountWithInvalidArgs(): void
    {
        // invalid key field
        $this->assertException(
            callback: Admin::class . "::disableUserAccount",
            args: [
                "key" => "invalid_field_name",
                "value" => "Bhathiya_123"
            ]
        );
    }

    function testEnableUserAccountWithEmptyArgs(): void
    {
        // empty key and value
        $this->assertFalse($this->admin->enableUserAccount(key: "", value: ""));

        // empty key
        $this->assertFalse($this->admin->enableUserAccount(key: "", value: "8"));

        // empty value
        $this->assertFalse($this->admin->enableUserAccount(key: "id", value: ""));
    }

    function testEnableUserAccountWithValidArgs(): void
    {
        // using id
        $this->assertTrue($this->admin->enableUserAccount(key: "id", value: "8"));

        // using username
        $this->assertTrue($this->admin->enableUserAccount(key: "username", value: "Bhathiya_123"));
    }

    function testEnableUserAccountWithInvalidArgs(): void
    {
        // using invalid field
        $this->assertException(
            callback: Admin::class . "::enableUserAccount",
            args: [
                "key" => "invalid_key_field",
                "value" => "Bhathiya_123"
            ]
        );
    }

    public function setup(): void
    {
        $this->admin = new Admin();
    }

    public function run(): void
    {
        $this->setup();

        // createAdmin
         $this->testCreateAdmin();

        // readAdmin
        $this->testReadAdminWithEmptyArguments();
        $this->testReadAdminWithValidArguments();
        $this->testReadAdminWithInvalidArguments();
        $this->testReadAdminWithInvalidFieldNames();

        // createUser
        $this->testCreateUserWithEmptyArgs();
        $this->testCreateUserWithValidArguments();
        $this->testCreateUserWithInvalidNumberOfArgs();

        // readUser
        $this->testReadUserWithEmptyArgs();
        $this->testReadUserWithValidArguments();
        $this->testReadUserWithInValidKeyField();
        $this->testReadUserWithNonExistingValue();

        // readAllUsers
        $this->testReadAllUsers();

        // getAllActiveUsers
        $this->testGetActiveUsers();

        // disableUserAccount
        $this->testDisableUserAccountWithEmptyArgs();
        $this->testDisableUserAccountWithValidArgs();
        $this->testDisableUserAccountWithInvalidArgs();

        // enableUserAccount
        $this->testEnableUserAccountWithEmptyArgs();
        $this->testEnableUserAccountWithValidArgs();
        $this->testEnableUserAccountWithInvalidArgs();


        $this->summary();
    }
}
