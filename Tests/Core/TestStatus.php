<?php

namespace Tests\Core;

require __DIR__ . '/../../vendor/autoload.php';

use Tests\Tester;
use Core\Status;

class TestStatus extends Tester
{

    public function __construct()
    {
        parent::__construct(Status::class);
    }

    function testContentType(): void
    {
        $this->assertEqualWithTypeCheck(Status::CONTENT_TYPE_JSON ,"Content-Type: application/json");
    }

    function testGetStatusCodeWithEmptyArgs(): void
    {
        $this->assertEqualWithTypeCheck(Status::getStatusCode("") ,500);
    }

    function testGetStatusCodeWithInvalidArgs(): void
    {
        $this->assertEqualWithTypeCheck(Status::getStatusCode("invalid") ,500);
        $this->assertEqualWithTypeCheck(Status::getStatusCode("some_more_invalid") ,500);
    }

    function testGetStatusWithValidArgs(): void
    {
        $this->assertEqualWithTypeCheck(Status::getStatusCode("success"),200);
        $this->assertEqualWithTypeCheck(Status::getStatusCode("error"),404);
        $this->assertEqualWithTypeCheck(Status::getStatusCode("unauthorized"),401);
        $this->assertEqualWithTypeCheck(Status::getStatusCode("forbidden"), 403);
    }

    public function run(): void
    {
        $this->testContentType();
        $this->testGetStatusCodeWithEmptyArgs();
        $this->testGetStatusCodeWithInvalidArgs();
        $this->testGetStatusWithValidArgs();
        $this->summary();
    }
}