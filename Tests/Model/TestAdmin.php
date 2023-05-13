<?php

namespace Tests\Model;

use App\Model\Admin;
use Tests\Tester;

class TestAdmin extends Tester
{
    private Admin $admin;

    public function __construct(string $unit_name)
    {
        parent::__construct($unit_name);
    }

    public function setup(): void
    {
        $this->admin = new Admin();
    }

    public function run(): void
    {
        $this->summary();
    }
}