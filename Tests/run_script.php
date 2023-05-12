<?php

require __DIR__ . '/../vendor/autoload.php';

use Tests\Core\TestValidator;
use Tests\Core\TestConfig;
use Tests\Model\TestUser;

$testValidator = new TestValidator();
$testValidator->run();

$testConfig = new TestConfig();
$testConfig->run();

$testUserModel = new TestUser();
$testUserModel->run();