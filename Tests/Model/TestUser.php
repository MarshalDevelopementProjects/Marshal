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
            args: ["username" => "kylo_ren"],
            message: ""
        );
    }

    public function run(): void
    {
        // TODO: Implement run() method.
        $this->setup();

        $this->testCreateUserWithEmptyArgs();

        $this->testCreateUserWithMissingArguments();

        $this->summary();
    }
}