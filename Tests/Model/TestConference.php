<?php

namespace Tests\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Conference;
use Tests\Tester;

class TestConference extends Tester
{

    private Conference $conference;

    public function __construct()
    {
        parent::__construct(Conference::class);
    }

    public function setup(): void
    {
        $this->conference = new Conference();
    }

    public function run(): void
    {
        $this->summary();
    }
}