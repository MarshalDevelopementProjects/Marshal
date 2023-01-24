<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controller\User\UserController;

// before you run the test cases make sure that you are logged in

function testViewProfile()
{
    $user_controller = new UserController();
    $user_controller->viewProfile(); // console.log(jsonData) to see the information sent from the backend 
}

function testEditProfile()
{
    $user_controller = new UserController(1);
    $user_controller->editProfile([
        "bio" => "I am Edward North, I work at Amazon and I am devops engineer there.
                  Interested in creating well documented secure systems.
                  And believe it or not I love Rust and Haskell",
        "email_address" => "ed_north@gmail.com",
        "username" => "ed_north",
        "first_name" => "Edward",
        "last_name" => "North",
        "phone_number" => "0789905105",
        "position" => "System Architect",
        "user_status" => "Busy"
    ]);
}

testViewProfile();
testEditProfile();
