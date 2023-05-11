<?php

namespace Tests\Core;

require __DIR__ . '/../../vendor/autoload.php';

use Core\Config;
use Tests\Tester;

class TestConfig extends Tester
{

    private Config $config;

    public function __construct()
    {
        parent::__construct(Config::class);
    }

    protected function setup(): void
    {
        $this->config =  new Config();
    }

    public function testGetApiGlobalWithNullValue(): void
    {
        $this->assertNull($this->config::getApiGlobal(null));
    }

    public function testGetApiGlobalWithInvalidKey(): void
    {
        $this->assertNull($this->config::getApiGlobal(key: "new"));
    }

    public function testGetApiGlobalWithValidKey(): void
    {
        $this->assertTrue(is_array($this->config::getApiGlobal(key: "remember")));
        $this->assertTrue(is_array($this->config::getApiGlobal(key: "mysql")));
    }

    public function run(): void
    {
        // TODO: Implement run() method.
        $this->setup();
        $this->testGetApiGlobalWithNullValue();
        $this->testGetApiGlobalWithInvalidKey();
        $this->testGetApiGlobalWithValidKey();
        $this->summary();
    }
}