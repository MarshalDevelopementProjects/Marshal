<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Admin;

function testCreateAdmin()
{
    $admin = new Admin();
    if ($admin->createAdmin(array(
        "username" => "SysAdmin",
        "first_name" => "Adam",
        "last_name" => "West",
        "email_address" => "adam_west@gmail.com",
        "password" => "1234567890",
        "street_address" => "No. 23, Top street",
        "city" => "York City",
        "country" => "England",
        "phone_number" => "+9470-9078923"
    ))) {
        echo "<pre>";
        echo "Admin successfully added to the database";
        echo "</pre>";
    } else {
        echo "<pre>";
        echo "Admin cannot be added to the database";
        echo "</pre>";
    }
}

function testReadAdmin()
{
    $admin = new Admin();
    if ($admin->readAdmin(key: "id", value: "admin63cd29b9531c8")) {
        echo "<pre>";
        var_dump($admin->getAdminData());
        echo "</pre>";
    } else {
        echo "<pre>";
        echo "Admin data cannot be retrieved from the database";
        echo "</pre>";
    }
}

function testReadAdminWithID()
{
    try {
        $admin = new Admin("admin63cd29b9531c8");
        echo "<pre>";
        var_dump($admin->getAdminData());
        echo "</pre>";
    } catch (\Exception $exception) {
        echo "<pre>";
        echo "Cannot read admin by ID";
        echo "</pre>";
        throw $exception;
    }
}

function testReadAdminWithUsername()
{
    try {
        $admin = new Admin();
        $admin->readAdmin(key: "username", value: "SysAdmin");
        echo "<pre>";
        var_dump($admin->getAdminData());
        echo "</pre>";
    } catch (\Exception $exception) {
        echo "<pre>";
        echo "Cannot read admin by ID";
        echo "</pre>";
        throw $exception;
    }
}

// testCreateAdmin();
testReadAdmin();
testReadAdminWithID();
testReadAdminWithUsername();
