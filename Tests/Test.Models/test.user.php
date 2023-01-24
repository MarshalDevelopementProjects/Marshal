<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\User;


function testCreateUser()
{
    $user = new User();
    if ($user->createUser(array(
        "username" => "ed_north",
        "first_name" => "Edward",
        "last_name" => "North",
        "email_address" => "edward_north@gmail.com",
        "password" => "1234567890",
        "phone_number" => "0773132798",
    ))) {
        echo "<pre>";
        echo "User successfully added to the database";
        echo "</pre>";
    } else {
        echo "<pre>";
        echo "User cannot be added to the database";
        echo "</pre>";
    }
}

function testReadUser()
{
    $user = new User();
    if ($user->readUser(key: "id", value: 1)) {
        echo "<pre>";
        var_dump($user->getUserData());
        echo "</pre>";
    } else {
        echo "<pre>";
        echo "User data cannot be retrieved from the database";
        echo "</pre>";
    }
}

function testReadUserWithID()
{
    try {
        $user = new User(1);
        echo "<pre>";
        var_dump($user->getUserData());
        echo "</pre>";
    } catch (\Exception $exception) {
        echo "<pre>";
        echo "Cannot read user by ID";
        echo "</pre>";
        throw $exception;
    }
}

function testReadUserWithUsername()
{
    try {
        $user = new User();
        $user->readUser(key: "username", value: "ed_north");
        echo "<pre>";
        var_dump($user->getUserData());
        echo "</pre>";
    } catch (\Exception $exception) {
        echo "<pre>";
        echo "Cannot read user by ID";
        echo "</pre>";
        throw $exception;
    }
}

function testUpdateUser()
{
    try {
        $user = new User(1);
        $user->updateUser("1", [
            "bio" => "I am Kylo Ren, I work at 99X and I am system architect there. Interested in creating well documented secure systems. And believe it or not I love Rust and Haskell",
            "email_address" => "kylo_ren@gmail.com",
            "username" => "kylo_ren",
            "first_name" => "Kylo",
            "last_name" => "Skywalker",
            "phone_number" => "0789902101",
            "position" => "System architect",
            "user_status" => "Busy"
        ]);
        echo "<pre>";
        var_dump($user->getUserData());
        echo "</pre>";
    } catch (\Exception $exception) {
        echo "<pre>";
        echo "Cannot read user by ID";
        echo "</pre>";
        throw $exception;
    }
}

testCreateUser();
testReadUser();
testReadUserWithID();
testReadUserWithUsername();
testReadUserWithUsername();
testUpdateUser();
