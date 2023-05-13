<?php

namespace Tests\Core;

require __DIR__ . '/../../vendor/autoload.php';

use Core\Validator\Validator;
use Tests\Tester;

class TestValidator extends Tester
{
    private Validator $validator;
    public function __construct()
    {
        parent::__construct(unit_name: Validator::class);
    }

    protected function setup(): void
    {
        $this->validator = new Validator();
    }

    public function testGetPassedBeforeRunningValidations(): void
    {
        $this->assertFalse($this->validator->getPassed());
    }

    public function testValidateWithEmptySchema():void
    {
        $this->assertException(
            callback: Validator::class . '::validate',
            args: [
                "values" => [
                    "username" => "ed_north",
                    "email_address" => "edward_north@gmail.com",
                    "password" => "1234567890",
                ],
                "schema" => ""
            ]
        );
    }

    public function testValidateWithEmptyValues():void
    {

        $this->validator->validate(values: [], schema: "login");
        $this->assertFalse($this->validator->getPassed());
    }

    public function testValidatorWithValidTestData(): void
    {
        $this->validator->validate(
            values: [
                "username" => "Bhathiya_123",
                "email_address" => "malingabhathiya@gmail.com",
                "password" => "1234567890",
            ],
            schema: "login"
        );
        $this->assertTrue($this->validator->getPassed());
    }

    public function testGetPassedAfterRunningValidValidations(): void
    {
        $this->assertTrue($this->validator->getPassed());
    }

    public function testValidatorWithInvalidTestData(): void
    {
        $this->validator->validate(
            values: [
                "username" => "ed_seaborn",
                "email_address" => "edward_seaborn@gmail.com",
                "password" => "1234567890",
            ],
            schema: "login"
        );
        $this->assertFalse($this->validator->getPassed());
    }

    public function testGetPassedAfterRunningInvalidValidations(): void
    {
        $this->assertFalse($this->validator->getPassed());
    }

    public function run(): void
    {
        // TODO: Implement run() method.
        $this->setup();

        $this->testGetPassedBeforeRunningValidations();

        $this->testValidateWithEmptySchema();

        $this->testValidateWithEmptyValues();

        $this->testValidatorWithValidTestData();
        $this->testGetPassedAfterRunningValidValidations();

        $this->testValidatorWithInvalidTestData();
        $this->testGetPassedAfterRunningInvalidValidations();
        $this->summary();
    }
}