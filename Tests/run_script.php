<?php

require __DIR__ . '/../vendor/autoload.php';

use Tests\Core\TestValidator;
use Tests\Core\TestConfig;

$testValidator = new TestValidator();
$testValidator->run();

$testConfig = new TestConfig();
$testConfig->run();