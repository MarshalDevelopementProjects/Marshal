<?php

namespace Tests\Core;

use Core\Cookie;
use Tests\Tester;

require __DIR__ . '/../../vendor/autoload.php';

class TestCookie extends Tester
{

    private Cookie $cookie;

    public function __construct()
    {
        parent::__construct(Cookie::class);
    }

    public function setup(): void
    {
        $this->cookie = new Cookie();
    }

    public function run(): void
    {
        $this->setup();
        $this->summary();
    }
}