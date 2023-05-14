<?php

require __DIR__ . '/../vendor/autoload.php';

use Tests\Core\TestFileUploader;
use Tests\Core\TestValidator;
use Tests\Core\TestConfig;
use Tests\Model\TestAdmin;
use Tests\Model\TestConference;
use Tests\Model\TestForum;
use Tests\Model\TestUser;

/*$testValidator = new TestValidator();
$testValidator->run();

$testConfig = new TestConfig();
$testConfig->run();

$testUserModel = new TestUser();
$testUserModel->run();

$testAdminModel = new TestAdmin();
$testAdminModel->run();

$testConference = new TestConference();
$testConference->run();

$testForum = new TestForum();
$testForum->run();

$testFileUploader = new TestFileUploader();
$testFileUploader->run();

$testStatus = new \Tests\Core\TestStatus();
$testStatus->run();
*/

$testAdminModel = new TestAdmin();
$testAdminModel->run();
